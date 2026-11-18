<?php

namespace App\Services;

use App\Jobs\ProcessCommentJob;
use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeCommentSyncService
{
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->clientId = (string) (config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID', ''));
        $this->clientSecret = (string) (config('services.google.client_secret') ?: env('GOOGLE_CLIENT_SECRET', ''));
    }

    /**
     * Synchronize comments from YouTube Data API v3 for a given YouTube channel
     *
     * @param Account $account
     * @param int $postLimit
     * @param int $limit
     * @param bool $queueAi
     * @return array ['success' => bool, 'synced_count' => int, 'message' => string]
     */
    public function syncAccountComments(Account $account, int $postLimit = 10, int $limit = 50, bool $queueAi = true): array
    {
        if (empty($account->access_token)) {
            return [
                'success' => false,
                'synced_count' => 0,
                'message' => 'No active access token for this YouTube channel.',
            ];
        }

        try {
            $accessToken = $this->ensureFreshAccessToken($account);

            $params = [
                'part' => 'snippet,replies',
                'maxResults' => min($limit, 100),
                'order' => 'time',
            ];

            if (!empty($account->youtube_channel_id)) {
                $params['allThreadsRelatedToChannelId'] = $account->youtube_channel_id;
            }

            $response = Http::timeout(20)
                ->withToken($accessToken)
                ->get('https://www.googleapis.com/youtube/v3/commentThreads', $params)
                ->json();

            if (isset($response['error'])) {
                $errorMsg = $response['error']['message'] ?? 'Failed to retrieve YouTube comment threads.';
                Log::warning("YouTube commentThreads error: {$errorMsg}", $response['error']);
                return [
                    'success' => false,
                    'synced_count' => 0,
                    'message' => $errorMsg,
                ];
            }

            $threads = $response['data'] ?? ($response['items'] ?? []);
            if (empty($threads)) {
                return [
                    'success' => true,
                    'synced_count' => 0,
                    'message' => 'No comments found for this YouTube channel.',
                ];
            }

            $syncedCount = 0;

            foreach ($threads as $thread) {
                $snippet = $thread['snippet'] ?? [];
                $topComment = $snippet['topLevelComment']['snippet'] ?? [];
                $topCommentId = (string) ($snippet['topLevelComment']['id'] ?? ($thread['id'] ?? uniqid('yt_c_')));
                $videoId = $snippet['videoId'] ?? null;

                if (!$videoId) {
                    continue;
                }

                // Match or create parent Post
                $post = Post::where('account_id', $account->id)
                    ->where(function ($q) use ($videoId) {
                        $q->where('platform_post_id', $videoId)
                          ->orWhere('meta_post_id', $videoId);
                    })->first();

                if (!$post) {
                    $post = Post::create([
                        'account_id'       => $account->id,
                        'meta_post_id'     => $videoId,
                        'platform_post_id' => $videoId,
                        'title'            => 'YouTube Video #' . $videoId,
                        'media_type'       => 'video',
                        'media_url'        => "https://www.youtube.com/watch?v={$videoId}",
                        'published_at'     => now(),
                    ]);
                }

                $authorName = $topComment['authorDisplayName'] ?? 'YouTube User';
                $authorPhoto = $topComment['authorProfileImageUrl'] ?? null;
                $authorMetaId = $topComment['authorChannelId']['value'] ?? null;
                $messageText = $topComment['textDisplay'] ?? ($topComment['textOriginal'] ?? '');
                $likeCount = (int) ($topComment['likeCount'] ?? 0);
                $commentedAt = isset($topComment['publishedAt']) ? Carbon::parse($topComment['publishedAt']) : now();

                $comment = Comment::firstOrNew([
                    'post_id' => $post->id,
                    'platform_comment_id' => $topCommentId,
                ]);

                $isNew = !$comment->exists;

                $comment->account_id = $account->id;
                $comment->commenter_name = $authorName;
                $comment->commenter_photo_url = $authorPhoto;
                $comment->commenter_meta_id = $authorMetaId;
                $comment->message = $messageText;
                $comment->likes_count = $likeCount;
                $comment->commented_at = $commentedAt;

                if ($isNew) {
                    $comment->status = Comment::STATUS_PENDING_REVIEW;
                }

                $comment->save();
                $syncedCount++;

                // Queue or run AI job processing
                if ($queueAi) {
                    ProcessCommentJob::dispatch($comment);
                } else {
                    (new ProcessCommentJob($comment))->handle();
                }
            }

            $account->update(['last_synced_at' => now()]);

            return [
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "Successfully synchronized {$syncedCount} YouTube comment(s).",
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to sync YouTube comments for account {$account->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'synced_count' => 0,
                'message' => 'YouTube Comment Sync Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Publish a new top-level comment on a YouTube Video
     */
    public function publishCommentOnVideo(Post $post, Account $account, string $message): array
    {
        if (empty($account->access_token)) {
            return ['success' => false, 'message' => 'No active access token for YouTube channel.'];
        }

        try {
            $accessToken = $this->ensureFreshAccessToken($account);
            $videoId = $post->platform_post_id ?: $post->meta_post_id;

            $payload = [
                'snippet' => [
                    'videoId' => $videoId,
                    'topLevelComment' => [
                        'snippet' => [
                            'textOriginal' => $message,
                        ],
                    ],
                ],
            ];

            $response = Http::timeout(20)
                ->withToken($accessToken)
                ->post('https://www.googleapis.com/youtube/v3/commentThreads?part=snippet', $payload)
                ->json();

            if (isset($response['error'])) {
                $msg = $response['error']['message'] ?? 'YouTube API error posting comment.';
                Log::warning("YouTube comment post error: {$msg}", $response['error']);
                return ['success' => false, 'message' => $msg];
            }

            $commentId = (string) ($response['id'] ?? ('yt_c_' . uniqid()));

            $comment = Comment::create([
                'post_id'             => $post->id,
                'account_id'          => $account->id,
                'platform_comment_id' => $commentId,
                'commenter_name'      => $account->name . ' (Channel)',
                'commenter_photo_url' => $account->profile_pic_url,
                'message'             => $message,
                'status'              => Comment::STATUS_REPLIED,
                'replied_at'          => now(),
                'commented_at'        => now(),
            ]);

            return ['success' => true, 'comment' => $comment, 'message' => 'Comment published successfully to YouTube.'];
        } catch (\Throwable $e) {
            Log::error("Failed to post comment to YouTube: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reply to an existing YouTube Comment
     */
    public function replyToComment(Comment $comment, string $replyMessage): array
    {
        $account = $comment->account;
        if (!$account || empty($account->access_token)) {
            return ['success' => false, 'message' => 'No active access token for YouTube channel.'];
        }

        try {
            $accessToken = $this->ensureFreshAccessToken($account);
            $parentId = $comment->platform_comment_id ?: $comment->meta_comment_id;

            $payload = [
                'snippet' => [
                    'parentId' => $parentId,
                    'textOriginal' => $replyMessage,
                ],
            ];

            $response = Http::timeout(20)
                ->withToken($accessToken)
                ->post('https://www.googleapis.com/youtube/v3/comments?part=snippet', $payload)
                ->json();

            if (isset($response['error'])) {
                $msg = $response['error']['message'] ?? 'YouTube API error posting reply.';
                Log::warning("YouTube reply error: {$msg}", $response['error']);
                return ['success' => false, 'message' => $msg];
            }

            $replyId = (string) ($response['id'] ?? ('yt_rep_' . uniqid()));

            // Update parent comment
            $comment->update([
                'status'        => Comment::STATUS_REPLIED,
                'ai_reply'      => $replyMessage,
                'meta_reply_id' => $replyId,
                'replied_at'    => now(),
            ]);

            return ['success' => true, 'message' => 'Reply posted successfully to YouTube.'];
        } catch (\Throwable $e) {
            Log::error("Failed to post reply to YouTube: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Set moderation status for a YouTube Comment (e.g. rejected to hide, or published to unhide)
     */
    public function setModerationStatus(Comment $comment, bool $hide = true): array
    {
        $account = $comment->account;
        if (!$account || empty($account->access_token)) {
            return ['success' => false, 'message' => 'No active access token for YouTube channel.'];
        }

        try {
            $accessToken = $this->ensureFreshAccessToken($account);
            $commentId = $comment->platform_comment_id ?: $comment->meta_comment_id;

            if (empty($commentId)) {
                return ['success' => false, 'message' => 'YouTube comment ID is missing.'];
            }

            $status = $hide ? 'rejected' : 'published';

            $response = Http::timeout(20)
                ->withToken($accessToken)
                ->post("https://www.googleapis.com/youtube/v3/comments/setModerationStatus?id={$commentId}&moderationStatus={$status}");

            if (!$response->successful()) {
                $err = $response->json()['error']['message'] ?? 'YouTube API error setting moderation status.';
                Log::warning("YouTube moderation status error: {$err}", $response->json() ?? []);
                return ['success' => false, 'message' => $err];
            }

            return [
                'success' => true,
                'message' => $hide ? 'Comment hidden on YouTube.' : 'Comment unhidden on YouTube.',
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to set moderation status on YouTube: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Ensure access token is valid, refreshing via Google OAuth if expired
     */
    protected function ensureFreshAccessToken(Account $account): string
    {
        $refreshToken = $account->google_refresh_token ?: $account->refresh_token;

        if ($account->token_expires_at && Carbon::parse($account->token_expires_at)->isPast() && !empty($refreshToken)) {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type'    => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['access_token'])) {
                    $account->update([
                        'access_token'     => $data['access_token'],
                        'token_expires_at' => now()->addSeconds((int) ($data['expires_in'] ?? 3600)),
                    ]);
                    return $data['access_token'];
                }
            }
        }

        return $account->access_token;
    }
}

