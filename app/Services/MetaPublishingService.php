<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Post;
use App\Models\ScheduledPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MetaPublishingService
{
    /**
     * Publish a scheduled post to Meta (Facebook Page or Instagram)
     *
     * @param ScheduledPost $scheduledPost
     * @return array ['success' => bool, 'meta_post_id' => ?string, 'message' => string]
     */
    public function publish(ScheduledPost $scheduledPost): array
    {
        $account = $scheduledPost->account;

        if (!$account || empty($account->access_token)) {
            $msg = 'Channel has no active Meta access token.';
            $scheduledPost->update([
                'status' => ScheduledPost::STATUS_FAILED,
                'error_message' => $msg,
            ]);
            return ['success' => false, 'message' => $msg];
        }

        $scheduledPost->update(['status' => ScheduledPost::STATUS_PUBLISHING]);

        try {
            if ($account->account_type == Account::TYPE_FACEBOOK) {
                $result = $this->publishToFacebookPage($scheduledPost, $account);
            } else {
                $result = $this->publishToInstagram($scheduledPost, $account);
            }

            if ($result['success']) {
                $metaPostId = $result['meta_post_id'];

                $scheduledPost->update([
                    'status' => ScheduledPost::STATUS_PUBLISHED,
                    'published_at' => now(),
                    'meta_post_id' => $metaPostId,
                    'error_message' => null,
                ]);

                // Sync into local posts feed for immediate visibility
                Post::firstOrCreate(
                    [
                        'account_id' => $account->id,
                        'meta_post_id' => $metaPostId,
                    ],
                    [
                        'message' => $scheduledPost->content,
                        'media_type' => $scheduledPost->post_type,
                        'media_url' => $scheduledPost->first_media_url,
                        'published_at' => now(),
                    ]
                );

                return ['success' => true, 'meta_post_id' => $metaPostId, 'message' => 'Post published successfully on Meta.'];
            } else {
                $scheduledPost->update([
                    'status' => ScheduledPost::STATUS_FAILED,
                    'error_message' => $result['message'],
                ]);
                return ['success' => false, 'message' => $result['message']];
            }
        } catch (\Throwable $e) {
            Log::error("Scheduled post publishing exception #{$scheduledPost->id}: {$e->getMessage()}");
            $scheduledPost->update([
                'status' => ScheduledPost::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Publish post to Facebook Page
     */
    protected function publishToFacebookPage(ScheduledPost $scheduledPost, Account $account): array
    {
        $postType = $scheduledPost->post_type;
        $content = $scheduledPost->content ?? '';
        $mediaUrls = $scheduledPost->media_urls ?? [];

        // 1. Text Only Post
        if ($postType === ScheduledPost::TYPE_TEXT || empty($mediaUrls)) {
            $url = "https://graph.facebook.com/v20.0/{$account->meta_account_id}/feed";
            $response = Http::timeout(30)->post($url, [
                'message' => $content,
                'access_token' => $account->access_token,
            ])->json();

            if (isset($response['error'])) {
                return ['success' => false, 'message' => $response['error']['message'] ?? 'Meta API rejected text post.'];
            }

            return ['success' => true, 'meta_post_id' => $response['id'] ?? null];
        }

        // 2. Video / Reel Post
        if (in_array($postType, [ScheduledPost::TYPE_VIDEO, ScheduledPost::TYPE_REEL])) {
            $videoPath = $mediaUrls[0] ?? null;
            $url = "https://graph-video.facebook.com/v20.0/{$account->meta_account_id}/videos";

            $fullPath = $this->resolveLocalDiskPath($videoPath);

            if ($fullPath && file_exists($fullPath)) {
                $response = Http::timeout(120)
                    ->attach('source', file_get_contents($fullPath), basename($fullPath))
                    ->post($url, [
                        'description' => $content,
                        'access_token' => $account->access_token,
                    ])->json();
            } else {
                $response = Http::timeout(120)->post($url, [
                    'file_url' => $scheduledPost->first_media_url,
                    'description' => $content,
                    'access_token' => $account->access_token,
                ])->json();
            }

            if (isset($response['error'])) {
                return ['success' => false, 'message' => $response['error']['message'] ?? 'Meta API rejected video post.'];
            }

            return ['success' => true, 'meta_post_id' => $response['id'] ?? null];
        }

        // 3. Photo Post (Single Image)
        $photoPath = $mediaUrls[0] ?? null;
        $url = "https://graph.facebook.com/v20.0/{$account->meta_account_id}/photos";
        $fullPath = $this->resolveLocalDiskPath($photoPath);

        if ($fullPath && file_exists($fullPath)) {
            $response = Http::timeout(60)
                ->attach('source', file_get_contents($fullPath), basename($fullPath))
                ->post($url, [
                    'caption' => $content,
                    'access_token' => $account->access_token,
                ])->json();
        } else {
            $response = Http::timeout(60)->post($url, [
                'url' => $scheduledPost->first_media_url,
                'caption' => $content,
                'access_token' => $account->access_token,
            ])->json();
        }

        if (isset($response['error'])) {
            return ['success' => false, 'message' => $response['error']['message'] ?? 'Meta API rejected photo post.'];
        }

        $metaPostId = $response['post_id'] ?? $response['id'] ?? null;
        return ['success' => true, 'meta_post_id' => $metaPostId];
    }

    /**
     * Publish post to Instagram Business account
     *
     * Supports both connection methods:
     *  - Direct Instagram Login → uses graph.instagram.com
     *  - Via Facebook Page → uses graph.facebook.com
     */
    protected function publishToInstagram(ScheduledPost $scheduledPost, Account $account): array
    {
        $igId = $account->meta_account_id;
        $content = $scheduledPost->content ?? '';
        $mediaUrl = $scheduledPost->first_media_url;

        if (empty($mediaUrl)) {
            return ['success' => false, 'message' => 'Instagram requires at least one photo or video.'];
        }

        // Determine API base URL based on connection method
        // Direct login: instagram_business_account_id == meta_account_id (self-reference)
        $isDirectLogin = $account->instagram_business_account_id === $account->meta_account_id;
        $apiBase = $isDirectLogin
            ? 'https://graph.instagram.com/v20.0'
            : 'https://graph.facebook.com/v20.0';

        // Step 1: Create Container
        $isReelOrVideo = in_array($scheduledPost->post_type, [ScheduledPost::TYPE_VIDEO, ScheduledPost::TYPE_REEL]);
        $containerUrl = "{$apiBase}/{$igId}/media";

        $params = [
            'caption' => $content,
            'access_token' => $account->access_token,
        ];

        if ($isReelOrVideo) {
            $params['media_type'] = 'REELS';
            $params['video_url'] = $mediaUrl;
        } else {
            $params['image_url'] = $mediaUrl;
        }

        $containerRes = Http::timeout(60)->post($containerUrl, $params)->json();

        if (isset($containerRes['error'])) {
            return ['success' => false, 'message' => $containerRes['error']['message'] ?? 'Failed to create Instagram container.'];
        }

        $creationId = $containerRes['id'] ?? null;
        if (!$creationId) {
            return ['success' => false, 'message' => 'Instagram did not return a media creation container.'];
        }

        // Step 1b: For Video and Reels, poll container status until Meta finishes processing
        if ($isReelOrVideo) {
            $maxPollAttempts = 30; // 30 attempts x 2s = 60s max wait time
            $isReady = false;

            for ($attempt = 1; $attempt <= $maxPollAttempts; $attempt++) {
                sleep(2);

                $statusRes = Http::timeout(15)->get("{$apiBase}/{$creationId}", [
                    'fields' => 'status_code',
                    'access_token' => $account->access_token,
                ])->json();

                $statusCode = $statusRes['status_code'] ?? null;

                if ($statusCode === 'FINISHED') {
                    $isReady = true;
                    break;
                } elseif ($statusCode === 'ERROR' || $statusCode === 'EXPIRED') {
                    $errorDetail = $statusRes['status'] ?? ($statusRes['error']['message'] ?? 'Processing failed on Instagram servers');
                    return [
                        'success' => false,
                        'message' => "Instagram video processing failed: {$errorDetail}",
                    ];
                }
            }

            if (!$isReady) {
                return [
                    'success' => false,
                    'message' => 'Instagram video processing is taking longer than expected. Please try publishing in a minute.',
                ];
            }
        }

        // Step 2: Publish Container
        $publishUrl = "{$apiBase}/{$igId}/media_publish";
        $publishRes = Http::timeout(60)->post($publishUrl, [
            'creation_id' => $creationId,
            'access_token' => $account->access_token,
        ])->json();

        if (isset($publishRes['error'])) {
            return ['success' => false, 'message' => $publishRes['error']['message'] ?? 'Failed to publish Instagram media container.'];
        }

        return ['success' => true, 'meta_post_id' => $publishRes['id'] ?? null];
    }

    /**
     * Resolve storage path to local absolute file path
     */
    protected function resolveLocalDiskPath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Clean relative prefix
        $cleanPath = preg_replace('/^(storage\/|\/storage\/)/', '', $path);

        if (Storage::disk('public')->exists($cleanPath)) {
            return Storage::disk('public')->path($cleanPath);
        }

        $directPath = public_path($path);
        if (file_exists($directPath)) {
            return $directPath;
        }

        return null;
    }
}

