<?php

namespace App\Services;

use App\Jobs\ProcessCommentJob;
use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCommentSyncService
{
    /**
     * Synchronize comments from Meta Graph API for posts of a given account
     *
     * @param Account $account
     * @param int $postLimit Max recent posts to inspect for comments
     * @param int $commentLimitPerPost Max comments to fetch per post
     * @return array ['success' => bool, 'synced_count' => int, 'message' => string]
     */
    public function syncAccountComments(Account $account, int $postLimit = 10, int $commentLimitPerPost = 25): array
    {
        if (empty($account->access_token)) {
            return [
                'success' => false,
                'synced_count' => 0,
                'message' => 'No active access token for this channel.',
            ];
        }

        // Fetch recent active posts belonging to this account
        $posts = Post::where('account_id', $account->id)
            ->orderBy('published_at', 'desc')
            ->limit($postLimit)
            ->get();

        if ($posts->isEmpty()) {
            return [
                'success' => true,
                'synced_count' => 0,
                'message' => 'No synced posts found. Please sync posts first before fetching comments.',
            ];
        }

        $totalSynced = 0;
        $errorCount = 0;
        $lastErrorMessage = null;

        foreach ($posts as $post) {
            try {
                $count = $this->syncCommentsForPost($post, $account, $commentLimitPerPost);
                $totalSynced += $count;
            } catch (\Throwable $e) {
                $errorCount++;
                $lastErrorMessage = $e->getMessage();
                Log::warning("Error syncing comments for post {$post->id}: {$e->getMessage()}");
            }
        }

        $account->update(['last_synced_at' => now()]);

        if ($errorCount > 0 && $totalSynced === 0) {
            return [
                'success' => false,
                'synced_count' => 0,
                'message' => "Comment sync notice: {$lastErrorMessage}",
            ];
        }

        return [
            'success' => true,
            'synced_count' => $totalSynced,
            'message' => "Successfully synchronized {$totalSynced} comment(s) across {$posts->count()} recent post(s).",
        ];
    }

    /**
     * Fetch & persist comments for a single Post from Meta
     * Supports both Facebook Page comments and Instagram Media comments
     */
    public function syncCommentsForPost(Post $post, Account $account, int $limit = 25): int
    {
        $isInstagram = $account->isInstagram();
        $url = "https://graph.facebook.com/v20.0/{$post->meta_post_id}/comments";

        // Adapt requested fields based on platform schema differences
        $fields = $isInstagram
            ? 'id,text,timestamp,username,from{id,username},like_count'
            : 'id,from{id,name,picture},message,created_time,parent';

        $response = Http::timeout(20)->get($url, [
            'fields' => $fields,
            'limit' => $limit,
            'access_token' => $account->access_token,
        ])->json();

        if (isset($response['error'])) {
            $msg = $response['error']['message'] ?? 'Failed to retrieve comments from Meta.';
            Log::warning("Meta comment fetch error on post {$post->id}: {$msg}", $response['error']);
            throw new \RuntimeException($msg);
        }

        $items = $response['data'] ?? [];
        $syncedForPost = 0;

        foreach ($items as $item) {
            $metaCommentId = (string) $item['id'];

            // Resilient text mapping (FB uses 'message', IG uses 'text')
            $message = $item['message'] ?? ($item['text'] ?? '');

            // Resilient timestamp mapping (FB uses 'created_time', IG uses 'timestamp')
            $rawTime = $item['created_time'] ?? ($item['timestamp'] ?? null);
            $commentedAt = $rawTime ? Carbon::parse($rawTime) : now();

            // Resilient author mapping (FB uses 'from.name', IG uses 'username')
            $from = $item['from'] ?? [];
            $commenterName = $from['name'] ?? ($item['username'] ?? ($from['username'] ?? 'User'));
            $commenterMetaId = isset($from['id']) ? (string) $from['id'] : null;
            $commenterPhoto = $from['picture']['data']['url'] ?? null;

            // Handle nested reply hierarchy if present
            $parentLocalCommentId = null;
            if (isset($item['parent']['id'])) {
                $parentMetaId = (string) $item['parent']['id'];
                $parentLocalComment = Comment::where('meta_comment_id', $parentMetaId)->first();
                $parentLocalCommentId = $parentLocalComment?->id;
            }

            // Persist without duplicates (safe idempotent upsert)
            $comment = Comment::firstOrNew([
                'post_id' => $post->id,
                'meta_comment_id' => $metaCommentId,
            ]);

            $comment->account_id = $account->id;
            $comment->parent_comment_id = $parentLocalCommentId;
            $comment->commenter_name = $commenterName;
            $comment->commenter_meta_id = $commenterMetaId;
            $comment->commenter_photo_url = $commenterPhoto;
            $comment->message = $message;
            $comment->commented_at = $commentedAt;

            $isNew = !$comment->exists;
            if ($isNew) {
                $comment->status = Comment::STATUS_PENDING_REVIEW;
            }

            $comment->save();

            // Dispatch AI analysis & moderation job
            if ($isNew || empty($comment->sentiment)) {
                ProcessCommentJob::dispatch($comment);
            }

            $syncedForPost++;
        }

        $post->update(['last_comment_sync_at' => now()]);

        return $syncedForPost;
    }

    /**
     * Publish a new top-level comment on a Post via Meta Graph API
     *
     * @param Post $post
     * @param Account $account
     * @param string $message
     * @return array ['success' => bool, 'comment' => ?Comment, 'message' => string]
     */
    public function publishCommentOnPost(Post $post, Account $account, string $message): array
    {
        if (empty($account->access_token)) {
            return ['success' => false, 'message' => 'No active access token for this channel.'];
        }

        $url = "https://graph.facebook.com/v20.0/{$post->meta_post_id}/comments";

        try {
            $response = Http::timeout(20)->post($url, [
                'message' => $message,
                'access_token' => $account->access_token,
            ])->json();

            if (isset($response['error'])) {
                $msg = $response['error']['message'] ?? 'Meta Graph API returned an error.';
                Log::warning("Meta comment creation error: {$msg}", $response['error']);
                return ['success' => false, 'message' => $msg];
            }

            $metaCommentId = (string) ($response['id'] ?? ('local_' . uniqid()));

            $comment = Comment::create([
                'post_id' => $post->id,
                'account_id' => $account->id,
                'meta_comment_id' => $metaCommentId,
                'commenter_name' => $account->name . ' (Page)',
                'commenter_photo_url' => $account->profile_pic_url,
                'message' => $message,
                'status' => Comment::STATUS_REPLIED,
                'replied_at' => now(),
                'commented_at' => now(),
            ]);

            return ['success' => true, 'comment' => $comment, 'message' => 'Comment published successfully.'];
        } catch (\Throwable $e) {
            Log::error("Failed to post comment to Meta: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reply to an existing Comment via Meta Graph API
     *
     * @param Comment $comment
     * @param string $replyMessage
     * @return array ['success' => bool, 'message' => string]
     */
    public function replyToComment(Comment $comment, string $replyMessage): array
    {
        $account = $comment->account;
        if (!$account || empty($account->access_token)) {
            return ['success' => false, 'message' => 'No active access token for this channel.'];
        }

        $commentId = $comment->effective_comment_id ?: $comment->meta_comment_id;
        $endpoint = ($account && $account->isInstagram()) ? 'replies' : 'comments';
        $url = "https://graph.facebook.com/v20.0/{$commentId}/{$endpoint}";

        try {
            $response = Http::timeout(20)->post($url, [
                'message' => $replyMessage,
                'access_token' => $account->access_token,
            ])->json();

            if (isset($response['error'])) {
                $msg = $response['error']['message'] ?? 'Meta Graph API returned an error.';
                Log::warning("Meta reply creation error: {$msg}", $response['error']);
                return ['success' => false, 'message' => $msg];
            }

            $metaReplyId = (string) ($response['id'] ?? null);

            // Update parent comment
            $comment->update([
                'status' => Comment::STATUS_REPLIED,
                'ai_reply' => $replyMessage,
                'meta_reply_id' => $metaReplyId,
                'replied_at' => now(),
            ]);

            // Also create child comment record if meta id returned
            if ($metaReplyId) {
                Comment::firstOrCreate(
                    [
                        'post_id' => $comment->post_id,
                        'meta_comment_id' => $metaReplyId,
                    ],
                    [
                        'account_id' => $account->id,
                        'parent_comment_id' => $comment->id,
                        'commenter_name' => $account->name . ' (Page)',
                        'commenter_photo_url' => $account->profile_pic_url,
                        'message' => $replyMessage,
                        'status' => Comment::STATUS_REPLIED,
                        'commented_at' => now(),
                        'replied_at' => now(),
                    ]
                );
            }

            return ['success' => true, 'message' => 'Reply posted successfully.'];
        } catch (\Throwable $e) {
            Log::error("Failed to post reply to Meta: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Hide or unhide a comment on Meta (Facebook Page or Instagram Business)
     *
     * @param Comment $comment
     * @param bool $hide True to hide, false to unhide
     * @return array ['success' => bool, 'message' => string]
     */
    public function toggleHideComment(Comment $comment, bool $hide = true): array
    {
        $account = $comment->account;
        if (!$account || empty($account->access_token)) {
            return ['success' => false, 'message' => 'No active access token for this channel.'];
        }

        $commentId = $comment->effective_comment_id ?: $comment->meta_comment_id;
        if (empty($commentId)) {
            return ['success' => false, 'message' => 'Comment ID is missing.'];
        }

        $url = "https://graph.facebook.com/v20.0/{$commentId}";

        // Facebook uses 'is_hidden', Instagram uses 'hide'
        $payload = [
            'access_token' => $account->access_token,
        ];

        if ($account->isInstagram()) {
            $payload['hide'] = $hide;
        } else {
            $payload['is_hidden'] = $hide;
        }

        try {
            $response = Http::timeout(20)->post($url, $payload)->json();

            if (isset($response['error'])) {
                $msg = $response['error']['message'] ?? 'Meta Graph API returned an error.';
                Log::warning("Meta hide/unhide error: {$msg}", [
                    'comment_id' => $comment->id,
                    'meta_comment_id' => $commentId,
                    'is_instagram' => $account->isInstagram(),
                    'error' => $response['error'],
                ]);
                return ['success' => false, 'message' => $msg];
            }

            return [
                'success' => true,
                'message' => $hide
                    ? 'Comment successfully hidden from public on ' . ($account->isInstagram() ? 'Instagram' : 'Facebook') . '.'
                    : 'Comment unhidden on ' . ($account->isInstagram() ? 'Instagram' : 'Facebook') . '.',
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to toggle hide comment on Meta: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
