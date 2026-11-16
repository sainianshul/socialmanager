<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaPostSyncService
{
    /**
     * Sync posts for a specific account from Meta Graph API
     *
     * @param Account $account
     * @param int $limit
     * @return array ['success' => bool, 'synced_count' => int, 'message' => string]
     */
    public function syncAccountPosts(Account $account, int $limit = 25): array
    {
        if (empty($account->access_token)) {
            return [
                'success' => false,
                'synced_count' => 0,
                'message' => 'No access token available for this account.',
            ];
        }

        try {
            if ($account->account_type == Account::TYPE_FACEBOOK) {
                return $this->syncFacebookPosts($account, $limit);
            } elseif ($account->account_type == Account::TYPE_INSTAGRAM) {
                return $this->syncInstagramMedia($account, $limit);
            }

            return [
                'success' => false,
                'synced_count' => 0,
                'message' => 'Unsupported platform type.',
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to sync posts for account {$account->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'synced_count' => 0,
                'message' => 'Meta API Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch & persist Facebook Page posts
     */
    protected function syncFacebookPosts(Account $account, int $limit): array
    {
        $url = "https://graph.facebook.com/v20.0/{$account->meta_account_id}/posts";

        // Query posts with safe resilient fields (attachments and post data)
        $response = Http::timeout(20)->get($url, [
            'fields' => 'id,message,created_time,permalink_url,attachments{media,type,url,subattachments},shares',
            'limit' => $limit,
            'access_token' => $account->access_token,
        ])->json();

        if (isset($response['error'])) {
            $msg = $response['error']['message'] ?? 'Failed to retrieve Facebook posts.';
            Log::warning("Facebook post fetch error: {$msg}", $response['error']);

            return [
                'success' => false,
                'synced_count' => 0,
                'message' => $msg,
            ];
        }

        $items = $response['data'] ?? [];
        $syncedCount = 0;

        foreach ($items as $item) {
            $metaPostId = (string) $item['id'];
            $message = $item['message'] ?? null;
            $publishedAt = isset($item['created_time']) ? Carbon::parse($item['created_time']) : now();
            $permalinkUrl = $item['permalink_url'] ?? null;

            // Extract attachment media
            $attachment = $item['attachments']['data'][0] ?? null;
            $mediaType = $attachment['type'] ?? 'status';
            $mediaUrl = $attachment['media']['image']['src'] ?? null;

            // Engagement metrics
            $sharesCount = (int) ($item['shares']['count'] ?? 0);

            Post::updateOrCreate(
                [
                    'account_id' => $account->id,
                    'meta_post_id' => $metaPostId,
                ],
                [
                    'message' => $message,
                    'media_type' => $mediaType,
                    'media_url' => $mediaUrl,
                    'permalink_url' => $permalinkUrl,
                    'likes_count' => 0,
                    'comments_count' => 0,
                    'shares_count' => $sharesCount,
                    'published_at' => $publishedAt,
                ]
            );

            $syncedCount++;
        }

        $account->update(['last_synced_at' => now()]);

        return [
            'success' => true,
            'synced_count' => $syncedCount,
            'message' => "Successfully synchronized {$syncedCount} posts from Facebook.",
        ];
    }

    /**
     * Fetch & persist Instagram Business Media
     *
     * Supports both connection methods:
     *  - Direct Instagram Login (uses graph.instagram.com)
     *  - Via Facebook Page (uses graph.facebook.com with instagram_business_account_id)
     */
    protected function syncInstagramMedia(Account $account, int $limit): array
    {
        // Determine correct API base URL and account ID
        // If instagram_business_account_id == meta_account_id, it was connected via direct Instagram login
        $isDirectLogin = $account->instagram_business_account_id === $account->meta_account_id;

        if ($isDirectLogin) {
            // Direct Instagram Login — use Instagram Graph API
            $url = "https://graph.instagram.com/v20.0/me/media";
        } else {
            // Connected via Facebook Page — use Facebook Graph API with IG Business Account ID
            $igId = $account->meta_account_id;
            $url = "https://graph.facebook.com/v20.0/{$igId}/media";
        }

        $response = Http::timeout(20)->get($url, [
            'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,like_count,comments_count,timestamp',
            'limit' => $limit,
            'access_token' => $account->access_token,
        ])->json();

        if (isset($response['error'])) {
            $msg = $response['error']['message'] ?? 'Failed to retrieve Instagram media.';
            return [
                'success' => false,
                'synced_count' => 0,
                'message' => $msg,
            ];
        }

        $items = $response['data'] ?? [];
        $syncedCount = 0;

        foreach ($items as $item) {
            $metaPostId = (string) $item['id'];
            $message = $item['caption'] ?? null;
            $publishedAt = isset($item['timestamp']) ? Carbon::parse($item['timestamp']) : now();
            $permalinkUrl = $item['permalink'] ?? null;
            $mediaType = strtolower($item['media_type'] ?? 'photo');
            $mediaUrl = $item['media_url'] ?? ($item['thumbnail_url'] ?? null);

            $likesCount = (int) ($item['like_count'] ?? 0);
            $commentsCount = (int) ($item['comments_count'] ?? 0);

            Post::updateOrCreate(
                [
                    'account_id' => $account->id,
                    'meta_post_id' => $metaPostId,
                ],
                [
                    'message' => $message,
                    'media_type' => $mediaType,
                    'media_url' => $mediaUrl,
                    'permalink_url' => $permalinkUrl,
                    'likes_count' => $likesCount,
                    'comments_count' => $commentsCount,
                    'shares_count' => 0,
                    'published_at' => $publishedAt,
                ]
            );

            $syncedCount++;
        }

        $account->update(['last_synced_at' => now()]);

        return [
            'success' => true,
            'synced_count' => $syncedCount,
            'message' => "Successfully synchronized {$syncedCount} items from Instagram.",
        ];
    }
}

