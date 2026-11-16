<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeVideoSyncService
{
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->clientId = (string) (config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID', ''));
        $this->clientSecret = (string) (config('services.google.client_secret') ?: env('GOOGLE_CLIENT_SECRET', ''));
    }

    /**
     * Sync videos for a specific YouTube channel using minimal API quota
     *
     * @param Account $account
     * @param int $limit
     * @return array ['success' => bool, 'synced_count' => int, 'message' => string]
     */
    public function syncChannelVideos(Account $account, int $limit = 25): array
    {
        if (empty($account->access_token)) {
            return [
                'success' => false,
                'synced_count' => 0,
                'message' => 'No access token available for this YouTube channel.',
            ];
        }

        try {
            $accessToken = $this->ensureFreshAccessToken($account);

            // Step 1: Get channel contentDetails to find uploads playlist ID (Cost: 1 unit)
            $uploadsPlaylistId = $this->getUploadsPlaylistId($account, $accessToken);

            if (!$uploadsPlaylistId) {
                // Fallback: YouTube convention replaces 'UC' prefix with 'UU'
                if ($account->youtube_channel_id && str_starts_with($account->youtube_channel_id, 'UC')) {
                    $uploadsPlaylistId = 'UU' . substr($account->youtube_channel_id, 2);
                } else {
                    return [
                        'success' => false,
                        'synced_count' => 0,
                        'message' => 'Could not determine uploads playlist for channel.',
                    ];
                }
            }

            // Step 2: Fetch playlist items (Cost: 1 unit)
            $playlistResponse = Http::timeout(20)
                ->withToken($accessToken)
                ->get('https://www.googleapis.com/youtube/v3/playlistItems', [
                    'part' => 'snippet,contentDetails',
                    'playlistId' => $uploadsPlaylistId,
                    'maxResults' => min($limit, 50),
                ])->json();

            if (isset($playlistResponse['error'])) {
                $errorMsg = $playlistResponse['error']['message'] ?? 'Failed to fetch playlist items.';
                Log::warning("YouTube playlistItems error: {$errorMsg}", $playlistResponse['error']);
                return [
                    'success' => false,
                    'synced_count' => 0,
                    'message' => $errorMsg,
                ];
            }

            $playlistItems = $playlistResponse['data'] ?? ($playlistResponse['items'] ?? []);
            if (empty($playlistItems)) {
                return [
                    'success' => true,
                    'synced_count' => 0,
                    'message' => 'No videos found in channel uploads playlist.',
                ];
            }

            $videoIds = [];
            foreach ($playlistItems as $item) {
                $vId = $item['contentDetails']['videoId'] ?? ($item['snippet']['resourceId']['videoId'] ?? null);
                if ($vId) {
                    $videoIds[] = $vId;
                }
            }

            if (empty($videoIds)) {
                return [
                    'success' => true,
                    'synced_count' => 0,
                    'message' => 'No valid video IDs found.',
                ];
            }

            // Step 3: Fetch video statistics, duration, and metadata (Cost: 1 unit for all IDs)
            $videosResponse = Http::timeout(20)
                ->withToken($accessToken)
                ->get('https://www.googleapis.com/youtube/v3/videos', [
                    'part' => 'snippet,contentDetails,statistics,status',
                    'id' => implode(',', $videoIds),
                ])->json();

            if (isset($videosResponse['error'])) {
                $errorMsg = $videosResponse['error']['message'] ?? 'Failed to fetch video details.';
                Log::warning("YouTube videos error: {$errorMsg}", $videosResponse['error']);
                return [
                    'success' => false,
                    'synced_count' => 0,
                    'message' => $errorMsg,
                ];
            }

            $videoItems = $videosResponse['data'] ?? ($videosResponse['items'] ?? []);
            $syncedCount = 0;

            foreach ($videoItems as $video) {
                $videoId = (string) $video['id'];
                $snippet = $video['snippet'] ?? [];
                $contentDetails = $video['contentDetails'] ?? [];
                $statistics = $video['statistics'] ?? [];
                $status = $video['status'] ?? [];

                $title = $snippet['title'] ?? 'Untitled Video';
                $description = $snippet['description'] ?? '';
                $publishedAt = isset($snippet['publishedAt']) ? Carbon::parse($snippet['publishedAt']) : now();

                // Determine best available thumbnail
                $thumbs = $snippet['thumbnails'] ?? [];
                $thumbnailUrl = $thumbs['maxres']['url']
                    ?? ($thumbs['standard']['url']
                    ?? ($thumbs['high']['url']
                    ?? ($thumbs['medium']['url']
                    ?? ($thumbs['default']['url'] ?? "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg"))));

                $durationSeconds = $this->parseDurationToSeconds($contentDetails['duration'] ?? '');
                $viewsCount = (int) ($statistics['viewCount'] ?? 0);
                $likesCount = (int) ($statistics['likeCount'] ?? 0);
                $commentsCount = (int) ($statistics['commentCount'] ?? 0);
                $tags = is_array($snippet['tags'] ?? null) ? $snippet['tags'] : [];
                $privacyStatus = $status['privacyStatus'] ?? 'public';
                $watchUrl = "https://www.youtube.com/watch?v={$videoId}";

                Post::updateOrCreate(
                    [
                        'account_id' => $account->id,
                        'meta_post_id' => $videoId,
                    ],
                    [
                        'platform_post_id' => $videoId,
                        'title' => $title,
                        'message' => $description,
                        'description' => $description,
                        'media_type' => 'video',
                        'media_url' => $watchUrl,
                        'thumbnail_url' => $thumbnailUrl,
                        'permalink_url' => $watchUrl,
                        'duration' => $durationSeconds,
                        'views_count' => $viewsCount,
                        'likes_count' => $likesCount,
                        'comments_count' => $commentsCount,
                        'tags' => $tags,
                        'privacy_status' => $privacyStatus,
                        'published_at' => $publishedAt,
                    ]
                );

                $syncedCount++;
            }

            return [
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "Successfully synchronized {$syncedCount} YouTube video(s).",
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to sync YouTube videos for account {$account->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'synced_count' => 0,
                'message' => 'YouTube Sync Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Resolve the uploads playlist ID for the YouTube channel
     */
    protected function getUploadsPlaylistId(Account $account, string $accessToken): ?string
    {
        $params = ['part' => 'contentDetails,statistics'];
        if (!empty($account->youtube_channel_id)) {
            $params['id'] = $account->youtube_channel_id;
        } else {
            $params['mine'] = 'true';
        }

        $res = Http::timeout(15)->withToken($accessToken)->get('https://www.googleapis.com/youtube/v3/channels', $params)->json();

        $items = $res['data'] ?? ($res['items'] ?? []);
        if (!empty($items[0])) {
            $channel = $items[0];

            // Update subscriber and video counts on the account if present
            if (isset($channel['statistics'])) {
                $account->update([
                    'subscriber_count' => (int) ($channel['statistics']['subscriberCount'] ?? $account->subscriber_count),
                    'video_count' => (int) ($channel['statistics']['videoCount'] ?? $account->video_count),
                ]);
            }

            return $channel['contentDetails']['relatedPlaylists']['uploads'] ?? null;
        }

        return null;
    }

    /**
     * Parse ISO 8601 duration string (e.g., PT15M33S, PT1H4M13S) into integer seconds
     */
    public function parseDurationToSeconds(string $isoDuration): int
    {
        if (empty($isoDuration)) {
            return 0;
        }

        try {
            $interval = new \DateInterval($isoDuration);
            return ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        } catch (\Throwable $e) {
            // Regex fallback if DateInterval encounters edge case
            if (preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/', $isoDuration, $matches)) {
                $hours = !empty($matches[1]) ? (int) $matches[1] : 0;
                $minutes = !empty($matches[2]) ? (int) $matches[2] : 0;
                $seconds = !empty($matches[3]) ? (int) $matches[3] : 0;
                return ($hours * 3600) + ($minutes * 60) + $seconds;
            }
            return 0;
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
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['access_token'])) {
                    $account->update([
                        'access_token' => $data['access_token'],
                        'token_expires_at' => now()->addSeconds((int) ($data['expires_in'] ?? 3600)),
                    ]);
                    return $data['access_token'];
                }
            }
        }

        return $account->access_token;
    }
}

