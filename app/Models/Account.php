<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    // Platform / Account Type Constants (Meta & Google/YouTube)
    public const TYPE_FACEBOOK = 1;
    public const TYPE_INSTAGRAM = 2;
    public const TYPE_YOUTUBE = 3;

    // Auto Reply Modes
    public const AUTO_REPLY_OFF           = 0; // Auto comment off
    public const AUTO_REPLY_WITH_APPROVAL = 1; // Auto comment with approval (Draft)
    public const AUTO_REPLY_AUTO_APPROVED = 2; // Auto comment and auto approved (Live Instant Reply)

    protected $fillable = [
        'user_id',
        'account_type',
        'meta_account_id',
        'instagram_business_account_id',
        'youtube_channel_id',
        'google_refresh_token',
        'name',
        'username',
        'category',
        'profile_pic_url',
        'followers_count',
        'following_count',
        'subscriber_count',
        'video_count',
        'platform_data',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'is_active',
        'webhook_active',
        'last_synced_at',
        'sync_interval_minutes',
        'ai_tone',
        'ai_language',
        'auto_reply_mode',
        'auto_hide_trolls',
        'comment_to_dm',
        'settings',
    ];

    protected $casts = [
        'account_type' => 'integer',
        'followers_count' => 'integer',
        'following_count' => 'integer',
        'subscriber_count' => 'integer',
        'video_count' => 'integer',
        'platform_data' => 'array',
        'is_active' => 'boolean',
        'webhook_active' => 'boolean',
        'auto_reply_mode' => 'integer',
        'auto_hide_trolls' => 'boolean',
        'comment_to_dm' => 'boolean',
        'settings' => 'array',
        'token_expires_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'sync_interval_minutes' => 'integer',
    ];

    /**
     * Hide sensitive credentials from JSON and array serialization
     */
    protected $hidden = [
        'access_token',
        'refresh_token',
        'google_refresh_token',
    ];

    /**
     * Transparently encrypt / decrypt access_token (AES-256)
     */
    protected function accessToken(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return $value;
                try {
                    return Crypt::decryptString($value);
                } catch (\Throwable) {
                    return $value; // Fallback for existing legacy plaintext
                }
            },
            set: function ($value) {
                if (empty($value)) return $value;
                try {
                    Crypt::decryptString($value);
                    return $value; // Already encrypted
                } catch (\Throwable) {
                    return Crypt::encryptString($value);
                }
            }
        );
    }

    /**
     * Transparently encrypt / decrypt refresh_token (AES-256)
     */
    protected function refreshToken(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return $value;
                try {
                    return Crypt::decryptString($value);
                } catch (\Throwable) {
                    return $value;
                }
            },
            set: function ($value) {
                if (empty($value)) return $value;
                try {
                    Crypt::decryptString($value);
                    return $value;
                } catch (\Throwable) {
                    return Crypt::encryptString($value);
                }
            }
        );
    }

    /**
     * Transparently encrypt / decrypt google_refresh_token (AES-256)
     */
    protected function googleRefreshToken(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return $value;
                try {
                    return Crypt::decryptString($value);
                } catch (\Throwable) {
                    return $value;
                }
            },
            set: function ($value) {
                if (empty($value)) return $value;
                try {
                    Crypt::decryptString($value);
                    return $value;
                } catch (\Throwable) {
                    return Crypt::encryptString($value);
                }
            }
        );
    }

    /**
     * Platform checks
     */
    public function isFacebook(): bool
    {
        return $this->account_type === self::TYPE_FACEBOOK;
    }

    public function isInstagram(): bool
    {
        return $this->account_type === self::TYPE_INSTAGRAM;
    }

    public function isYouTube(): bool
    {
        return $this->account_type === self::TYPE_YOUTUBE;
    }

    /**
     * Get platform options list
     */
    public static function getAccountTypeList(): array
    {
        return [
            self::TYPE_FACEBOOK => 'Facebook Page',
            self::TYPE_INSTAGRAM => 'Instagram Business',
            self::TYPE_YOUTUBE => 'YouTube Channel',
        ];
    }

    /**
     * Auto Reply Mode Helpers
     */
    public function isAutoReplyOff(): bool
    {
        return (int) $this->auto_reply_mode === self::AUTO_REPLY_OFF;
    }

    public function isAutoReplyWithApproval(): bool
    {
        return (int) $this->auto_reply_mode === self::AUTO_REPLY_WITH_APPROVAL;
    }

    public function isAutoReplyAutoApproved(): bool
    {
        return (int) $this->auto_reply_mode === self::AUTO_REPLY_AUTO_APPROVED;
    }

    public static function getAutoReplyModeList(): array
    {
        return [
            self::AUTO_REPLY_OFF           => 'Auto comment off',
            self::AUTO_REPLY_WITH_APPROVAL => 'Auto Comment With approved',
            self::AUTO_REPLY_AUTO_APPROVED => 'Auto comment and Auto approved',
        ];
    }

    /**
     * Get human-readable name for account type
     */
    public function getAccountTypeNameAttribute(): string
    {
        return self::getAccountTypeList()[$this->account_type] ?? 'Unknown';
    }

    /**
     * Get Tabler badge color according to platform
     */
    public function getPlatformColorAttribute(): string
    {
        return match ($this->account_type) {
            self::TYPE_FACEBOOK => 'facebook',
            self::TYPE_INSTAGRAM => 'pink',
            self::TYPE_YOUTUBE => 'red',
            default => 'secondary',
        };
    }

    /**
     * Get Tabler icon class according to platform
     */
    public function getPlatformIconAttribute(): string
    {
        return match ($this->account_type) {
            self::TYPE_FACEBOOK => 'ti ti-brand-facebook',
            self::TYPE_INSTAGRAM => 'ti ti-brand-instagram',
            self::TYPE_YOUTUBE => 'ti ti-brand-youtube',
            default => 'ti ti-world',
        };
    }

    /**
     * Get Tabler light badge classes
     */
    public function getPlatformBadgeClassAttribute(): string
    {
        return match ($this->account_type) {
            self::TYPE_FACEBOOK => 'badge-outline text-blue',
            self::TYPE_INSTAGRAM => 'badge-outline text-pink',
            self::TYPE_YOUTUBE => 'badge-outline text-red',
            default => 'badge-outline text-secondary',
        };
    }

    /**
     * Scopes for platform filtering
     */
    public function scopeFacebook($query)
    {
        return $query->where('account_type', self::TYPE_FACEBOOK);
    }

    public function scopeInstagram($query)
    {
        return $query->where('account_type', self::TYPE_INSTAGRAM);
    }

    public function scopeYouTube($query)
    {
        return $query->where('account_type', self::TYPE_YOUTUBE);
    }

    public function scopeForPlatform($query, ?int $platform)
    {
        if ($platform) {
            return $query->where('account_type', $platform);
        }
        return $query;
    }

    /**
     * Account owner relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if account has an auto-renewing refresh token (e.g. YouTube via Google OAuth)
     */
    public function hasAutoRenewingToken(): bool
    {
        return $this->isYouTube() && !empty($this->google_refresh_token);
    }

    /**
     * Check if access token is expired.
     * Note: YouTube accounts with a valid google_refresh_token auto-renew automatically,
     * so they are not permanently expired unless the refresh token is missing.
     */
    public function isTokenExpired(): bool
    {
        if ($this->hasAutoRenewingToken()) {
            return false;
        }

        return $this->token_expires_at !== null && $this->token_expires_at->isPast();
    }

    /**
     * Check if access token expires within given number of days
     */
    public function isTokenExpiringSoon(int $days = 7): bool
    {
        if ($this->hasAutoRenewingToken()) {
            return false;
        }

        return $this->token_expires_at !== null 
            && !$this->isTokenExpired() 
            && $this->token_expires_at <= now()->addDays($days);
    }

    /**
     * Token status badge HTML (Transparent outline with border)
     */
    public function getTokenStatusBadgeAttribute(): string
    {
        if ($this->isTokenExpired()) {
            return '<span class="badge badge-outline text-danger"><i class="ti ti-alert-triangle me-1"></i>Token Expired</span>';
        }

        if ($this->hasAutoRenewingToken()) {
            return '<span class="badge badge-outline text-success"><i class="ti ti-refresh me-1"></i>Auto-Renewing</span>';
        }

        if ($this->isTokenExpiringSoon()) {
            $days = max(1, (int) now()->diffInDays($this->token_expires_at));
            return '<span class="badge badge-outline text-warning"><i class="ti ti-clock-exclamation me-1"></i>Expires in ' . $days . 'd</span>';
        }

        return '<span class="badge badge-outline text-success"><i class="ti ti-circle-check me-1"></i>Token Active</span>';
    }

    /**
     * Synced posts belonging to this account
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Comments belonging to this account
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Scheduled posts for this account
     */
    public function scheduledPosts(): HasMany
    {
        return $this->hasMany(ScheduledPost::class);
    }

    /**
     * Auto DM rules for this account
     */
    public function autoDmRules(): HasMany
    {
        return $this->hasMany(AutoDmRule::class);
    }

    /**
     * Calculate the effective sync polling interval in minutes:
     * - If manual interval is configured (> 0), respects user choice (10m to 360m / 6 hours).
     * - Otherwise (0 = Smart Auto-Adaptive), dynamically scales based on activity:
     *   * Hot (Active in last 6 hours)  -> 15 minutes
     *   * Warm (Active in last 24 hours) -> 30 minutes
     *   * Moderate (Active in last 3 days) -> 60 minutes (1 hour)
     *   * Cool (Active in last 7 days)   -> 120 minutes (2 hours)
     *   * Cold / Dormant (> 7 days inactive) -> 360 minutes (6 hours)
     */
    public function getEffectiveSyncIntervalMinutes(): int
    {
        // 1. If user set an explicit manual interval (> 0), respect it (clamped between 10m and 360m)
        if (!empty($this->sync_interval_minutes) && $this->sync_interval_minutes > 0) {
            return max(10, min(360, (int) $this->sync_interval_minutes));
        }

        // 2. Smart Auto-Adaptive:
        // Find most recent post or comment activity timestamp
        $latestPostAt = null;
        try {
            $latestPostAt = $this->posts()->max('published_at');
        } catch (\Throwable $e) {}

        $latestCommentAt = null;
        try {
            $latestCommentAt = $this->comments()->max('commented_at');
        } catch (\Throwable $e) {}

        $mostRecent = null;
        if ($latestPostAt && $latestCommentAt) {
            $mostRecent = max(Carbon::parse($latestPostAt), Carbon::parse($latestCommentAt));
        } elseif ($latestPostAt) {
            $mostRecent = Carbon::parse($latestPostAt);
        } elseif ($latestCommentAt) {
            $mostRecent = Carbon::parse($latestCommentAt);
        }

        // If new account with no history, start with standard 30 minutes
        if (!$mostRecent) {
            return 30;
        }

        $hoursSince = now()->diffInHours($mostRecent);

        // Tier A: Hot (activity within last 6 hours) -> 15 min
        if ($hoursSince <= 6) {
            return 15;
        }

        // Tier B: Warm (activity within last 24 hours) -> 30 min
        if ($hoursSince <= 24) {
            return 30;
        }

        // Tier C: Moderate (activity within last 3 days) -> 1 hour
        if ($hoursSince <= 72) {
            return 60;
        }

        // Tier D: Cool (activity within last 7 days) -> 2 hours
        if ($hoursSince <= 168) {
            return 120;
        }

        // Tier E: Cold / Dormant (> 7 days inactive) -> 6 hours (Max relaxation to save API calls)
        return 360;
    }

    /**
     * Determine if this account is due for polling sync right now
     */
    public function isDueForSync(): bool
    {
        if (!$this->is_active || empty($this->access_token)) {
            return false;
        }

        if (empty($this->last_synced_at)) {
            return true;
        }

        $intervalMinutes = $this->getEffectiveSyncIntervalMinutes();

        return $this->last_synced_at->lessThanOrEqualTo(now()->subMinutes($intervalMinutes));
    }

    /**
     * Human-readable sync status and interval label
     */
    public function getSyncStatusLabelAttribute(): string
    {
        $minutes = $this->getEffectiveSyncIntervalMinutes();
        $isAuto = empty($this->sync_interval_minutes) || $this->sync_interval_minutes == 0;

        $timeStr = match (true) {
            $minutes < 60 => "{$minutes}m",
            $minutes == 60 => '1h',
            $minutes == 120 => '2h',
            $minutes == 360 => '6h',
            default => round($minutes / 60, 1) . 'h',
        };

        return $isAuto ? "Auto ({$timeStr})" : "Manual ({$timeStr})";
    }
}

