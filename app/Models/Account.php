<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

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
}

