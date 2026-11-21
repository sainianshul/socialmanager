<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Comment;
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
                $localPost = Post::firstOrCreate(
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

                // Automatically publish First Comment if set (e.g. hashtags, links)
                if (!empty($scheduledPost->first_comment)) {
                    $this->publishFirstComment($scheduledPost, $account, $metaPostId, $localPost);
                }

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
            $params['video_url'] = $this->ensurePublicHttpsUrl($mediaUrl);
        } else {
            // Instagram Content Publishing API strictly requires JPEG for photo containers.
            // Automatically converts PNG/WebP/GIF to JPEG if needed and ensures public HTTPS URL.
            $jpegUrl = $this->ensureInstagramPhotoUrl($scheduledPost);
            $params['image_url'] = $jpegUrl ?: $this->ensurePublicHttpsUrl($mediaUrl);
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
     * Automatically publish the First Comment on Facebook or Instagram right after post goes live.
     */
    protected function publishFirstComment(ScheduledPost $scheduledPost, Account $account, string $metaPostId, ?Post $localPost = null): void
    {
        if (empty($scheduledPost->first_comment) || empty($account->access_token)) {
            return;
        }

        try {
            $apiBase = config('services.meta.graph_url', 'https://graph.facebook.com/v20.0');
            $commentUrl = "{$apiBase}/{$metaPostId}/comments";

            $response = Http::timeout(20)->post($commentUrl, [
                'message'      => $scheduledPost->first_comment,
                'access_token' => $account->access_token,
            ])->json();

            if (isset($response['id'])) {
                $metaCommentId = (string) $response['id'];
                Log::info("First comment successfully published on post #{$scheduledPost->id} (Meta ID {$metaPostId}): {$metaCommentId}");

                if ($localPost) {
                    Comment::create([
                        'post_id'             => $localPost->id,
                        'account_id'          => $account->id,
                        'meta_comment_id'     => $metaCommentId,
                        'commenter_name'      => $account->name,
                        'commenter_photo_url' => $account->profile_pic_url,
                        'message'             => $scheduledPost->first_comment,
                        'status'              => Comment::STATUS_REPLIED,
                        'replied_at'          => now(),
                        'commented_at'        => now(),
                    ]);
                }
            } else {
                $errorMsg = $response['error']['message'] ?? 'Unknown error';
                Log::warning("First comment publish failed for post #{$scheduledPost->id}: {$errorMsg}", $response ?? []);
            }
        } catch (\Throwable $e) {
            Log::error("First comment exception for post #{$scheduledPost->id}: {$e->getMessage()}");
        }
    }

    /**
     * Resolve storage path to local absolute file path
     */
    public function resolveLocalDiskPath(?string $path): ?string
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

    /**
     * Ensure Instagram feed photo is in valid JPEG format as strictly required by Meta Graph API.
     * Automatically converts PNG, WebP, or GIF media to high-quality JPEG and returns public HTTPS URL.
     */
    public function ensureInstagramPhotoUrl(ScheduledPost $scheduledPost): ?string
    {
        $mediaUrls = $scheduledPost->media_urls ?? [];
        $rawPath = !empty($scheduledPost->thumbnail_path) ? $scheduledPost->thumbnail_path : ($mediaUrls[0] ?? null);

        if (empty($rawPath)) {
            return null;
        }

        $localPath = $this->resolveLocalDiskPath($rawPath);
        if (!$localPath || !file_exists($localPath)) {
            return $this->ensurePublicHttpsUrl($scheduledPost->first_media_url);
        }

        $extension = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));

        // If already JPEG, return its direct HTTPS URL
        if (in_array($extension, ['jpg', 'jpeg'])) {
            return $this->ensurePublicHttpsUrl($scheduledPost->first_media_url);
        }

        // Convert non-JPEG formats (PNG, WebP, GIF) to JPEG using GD
        try {
            $hash = md5_file($localPath);
            $convertedRelPath = 'posts/instagram_converted/' . $hash . '.jpg';
            $convertedDiskPath = Storage::disk('public')->path($convertedRelPath);

            if (!file_exists($convertedDiskPath)) {
                $dir = dirname($convertedDiskPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $sourceImage = null;
                if ($extension === 'png') {
                    $sourceImage = @imagecreatefrompng($localPath);
                } elseif ($extension === 'webp') {
                    $sourceImage = @imagecreatefromwebp($localPath);
                } elseif ($extension === 'gif') {
                    $sourceImage = @imagecreatefromgif($localPath);
                }

                if ($sourceImage) {
                    $w = imagesx($sourceImage);
                    $h = imagesy($sourceImage);
                    $canvas = imagecreatetruecolor($w, $h);

                    // Fill white background for transparent PNGs
                    $white = imagecolorallocate($canvas, 255, 255, 255);
                    imagefill($canvas, 0, 0, $white);

                    imagecopy($canvas, $sourceImage, 0, 0, 0, 0, $w, $h);
                    imagejpeg($canvas, $convertedDiskPath, 95);

                    imagedestroy($sourceImage);
                    imagedestroy($canvas);
                }
            }

            if (file_exists($convertedDiskPath)) {
                $convertedUrl = Storage::disk('public')->url($convertedRelPath);
                return $this->ensurePublicHttpsUrl($convertedUrl);
            }
        } catch (\Throwable $e) {
            Log::warning("Instagram photo JPEG conversion failed for post #{$scheduledPost->id}: {$e->getMessage()}");
        }

        return $this->ensurePublicHttpsUrl($scheduledPost->first_media_url);
    }

    /**
     * Ensure URL is publicly reachable via HTTPS without localhost or 301 redirect issues.
     */
    public function ensurePublicHttpsUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $appUrl = rtrim(config('app.url') ?? env('APP_URL') ?? '', '/');

        // If relative path starting with '/', prefix with appUrl
        if (str_starts_with($url, '/')) {
            $url = $appUrl . $url;
        }

        // Replace localhost or 127.0.0.1 host with APP_URL
        if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?(/.*)?$#i', $url, $matches)) {
            $path = $matches[3] ?? '';
            $url = $appUrl . $path;
        }

        // Meta requires HTTPS in production; avoid 301 text/html redirects
        if (str_starts_with($url, 'http://') && !str_contains($url, 'localhost') && !str_contains($url, '127.0.0.1')) {
            $url = 'https://' . substr($url, 7);
        }

        return $url;
    }
}

