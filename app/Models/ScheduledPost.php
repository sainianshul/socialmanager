<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledPost extends Model
{
    use HasFactory, SoftDeletes;

    // Workflow Status Constants
    public const STATUS_DRAFT      = 0;
    public const STATUS_SCHEDULED  = 1;
    public const STATUS_PUBLISHING = 2;
    public const STATUS_PUBLISHED  = 3;
    public const STATUS_FAILED     = 4;
    public const STATUS_CANCELLED  = 5;

    // Target Platform Constants
    public const PLATFORM_FACEBOOK  = 1;
    public const PLATFORM_INSTAGRAM = 2;
    public const PLATFORM_YOUTUBE   = 3;

    // Post Types
    public const TYPE_TEXT     = 'text';
    public const TYPE_PHOTO    = 'photo';
    public const TYPE_VIDEO    = 'video';
    public const TYPE_REEL     = 'reel';
    public const TYPE_CAROUSEL = 'carousel';

    protected $fillable = [
        'user_id',
        'account_id',
        'platform',
        'post_type',
        'title',
        'content',
        'first_comment',
        'description',
        'media_urls',
        'tags',
        'privacy_status',
        'thumbnail_path',
        'scheduled_at',
        'published_at',
        'status',
        'error_message',
        'meta_post_id',
    ];

    protected $casts = [
        'platform'     => 'integer',
        'media_urls'   => 'array',
        'tags'         => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'status'       => 'integer',
    ];

    /**
     * User who created the scheduled post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Target social channel account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Status label mapping
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_DRAFT      => 'Draft',
            self::STATUS_SCHEDULED  => 'Scheduled',
            self::STATUS_PUBLISHING => 'Publishing...',
            self::STATUS_PUBLISHED  => 'Published',
            self::STATUS_FAILED     => 'Failed',
            self::STATUS_CANCELLED  => 'Cancelled',
        ];
    }

    public function getStatusNameAttribute(): string
    {
        return self::getStatusList()[$this->status] ?? 'Unknown';
    }

    /**
     * Platform checks
     */
    public function isFacebook(): bool
    {
        return $this->platform === self::PLATFORM_FACEBOOK;
    }

    public function isInstagram(): bool
    {
        return $this->platform === self::PLATFORM_INSTAGRAM;
    }

    public function isYouTube(): bool
    {
        return $this->platform === self::PLATFORM_YOUTUBE;
    }

    public function getPlatformNameAttribute(): string
    {
        return match ($this->platform) {
            self::PLATFORM_FACEBOOK  => 'Facebook',
            self::PLATFORM_INSTAGRAM => 'Instagram',
            self::PLATFORM_YOUTUBE   => 'YouTube',
            default                  => 'Social',
        };
    }

    public function getPlatformIconAttribute(): string
    {
        return match ($this->platform) {
            self::PLATFORM_FACEBOOK  => 'ti ti-brand-facebook',
            self::PLATFORM_INSTAGRAM => 'ti ti-brand-instagram',
            self::PLATFORM_YOUTUBE   => 'ti ti-brand-youtube',
            default                  => 'ti ti-share',
        };
    }

    /**
     * Check if post can be published immediately.
     * Only pending/scheduled/future posts (not already published or currently publishing) can be published.
     */
    public function canBePublishedNow(): bool
    {
        return $this->status !== self::STATUS_PUBLISHED
            && $this->status !== self::STATUS_PUBLISHING
            && in_array($this->status, [self::STATUS_SCHEDULED, self::STATUS_DRAFT, self::STATUS_FAILED]);
    }

    /**
     * Check if post can be edited.
     * Allowed only if post is not published and not currently publishing.
     */
    public function isEditable(): bool
    {
        return $this->status !== self::STATUS_PUBLISHED
            && $this->status !== self::STATUS_PUBLISHING;
    }

    public function getPlatformBadgeAttribute(): string
    {
        return match ($this->platform) {
            self::PLATFORM_FACEBOOK  => '<span class="badge badge-outline text-blue"><i class="ti ti-brand-facebook me-1"></i>Facebook</span>',
            self::PLATFORM_INSTAGRAM => '<span class="badge badge-outline text-pink"><i class="ti ti-brand-instagram me-1"></i>Instagram</span>',
            self::PLATFORM_YOUTUBE   => '<span class="badge badge-outline text-red"><i class="ti ti-brand-youtube me-1"></i>YouTube</span>',
            default                  => '<span class="badge badge-outline text-secondary">Social</span>',
        };
    }

    /**
     * Tabler badge HTML for status
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT      => '<span class="badge badge-outline text-secondary"><i class="ti ti-file-pencil me-1"></i>Draft</span>',
            self::STATUS_SCHEDULED  => '<span class="badge badge-outline text-azure"><i class="ti ti-clock me-1"></i>Scheduled</span>',
            self::STATUS_PUBLISHING => '<span class="badge badge-outline text-warning"><i class="ti ti-loader animate-spin me-1"></i>Publishing</span>',
            self::STATUS_PUBLISHED  => '<span class="badge badge-outline text-success"><i class="ti ti-check me-1"></i>Published</span>',
            self::STATUS_FAILED     => '<span class="badge badge-outline text-danger"><i class="ti ti-alert-triangle me-1"></i>Failed</span>',
            self::STATUS_CANCELLED  => '<span class="badge badge-outline text-secondary"><i class="ti ti-x me-1"></i>Cancelled</span>',
            default                 => '<span class="badge badge-outline text-secondary">—</span>',
        };
    }

    /**
     * Tabler badge HTML for post type
     */
    public function getPostTypeBadgeAttribute(): string
    {
        return match ($this->post_type) {
            self::TYPE_TEXT     => '<span class="badge badge-outline text-secondary"><i class="ti ti-typography me-1"></i>Text</span>',
            self::TYPE_PHOTO    => '<span class="badge badge-outline text-blue"><i class="ti ti-photo me-1"></i>Photo</span>',
            self::TYPE_CAROUSEL => '<span class="badge badge-outline text-azure"><i class="ti ti-photo-circle me-1"></i>Carousel</span>',
            self::TYPE_VIDEO    => '<span class="badge badge-outline text-indigo"><i class="ti ti-video me-1"></i>Video</span>',
            self::TYPE_REEL     => '<span class="badge badge-outline text-purple"><i class="ti ti-player-play me-1"></i>Reel</span>',
            default             => '<span class="badge badge-outline text-secondary">' . e(ucfirst($this->post_type)) . '</span>',
        };
    }

    /**
     * Get first media item URL for thumbnail display
     */
    public function getFirstMediaUrlAttribute(): ?string
    {
        $rawPath = !empty($this->thumbnail_path) ? $this->thumbnail_path : (!empty($this->media_urls[0]) ? $this->media_urls[0] : null);

        if (!$rawPath) {
            return null;
        }

        if (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
            $url = $rawPath;
        } else {
            $cleanPath = ltrim($rawPath, '/');
            $appUrl = rtrim(config('app.url') ?? env('APP_URL') ?? '', '/');

            if (!empty($appUrl) && !str_contains($appUrl, 'localhost:8001')) {
                $url = $appUrl . '/storage/' . $cleanPath;
            } else {
                $url = asset('storage/' . $cleanPath);
            }
        }

        // External platforms like Instagram require direct HTTPS in production environments
        if (str_starts_with($url, 'http://') && !str_contains($url, 'localhost') && !str_contains($url, '127.0.0.1')) {
            $url = 'https://' . substr($url, 7);
        }

        return $url;
    }

    /**
     * Scope for posts ready to be dispatched
     */
    public function scopeDueForPublishing($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('scheduled_at', '<=', now());
    }

    /**
     * Scope for upcoming scheduled posts
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at', 'asc');
    }

    /**
     * Scope for filtering by platform
     */
    public function scopeForPlatform($query, ?int $platform)
    {
        if ($platform) {
            return $query->where('platform', $platform);
        }
        return $query;
    }
}
