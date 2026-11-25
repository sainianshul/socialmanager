<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    const ROLE_ADMIN = 1;
    const ROLE_MANAGER = 2;
    const ROLE_USER = 3; // Both Buyer and Seller

    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;
    const STATUS_SUSPENDED = 3;

    const CLIENT_TYPE_POLITICIAN = 'politician';
    const CLIENT_TYPE_BUSINESS = 'business';
    const CLIENT_TYPE_PERSONAL = 'personal';

    const CREATED_BY_SELF = 0;
    const CREATED_BY_ADMIN = 1;


    protected $fillable = [
        'profile_completed_at',
        'name',
        'phone',
        'email',
        'password',
        'role',
        'client_type',
        'profile_photo',
        'status',
        'created_by',
        'blocked_reason',
        'phone_verified_at',
        'last_login_at',
        'pincode',
        'address',
        'city',
        'state',
        'latitude',
        'longitude',
        'location_updated_at',
        'canva_access_token',
        'canva_refresh_token',
        'canva_token_expires_at',
        'can_schedule_posts',
        'can_use_festival_studio',
        'can_use_auto_dm',
        'can_use_ai',
        'ai_token_quota',
        'ai_tokens_used',
        'ai_quota_resets_at',
        'can_manage_users',
        'can_manage_content',
        'can_manage_tickets',
        'can_access_system',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'canva_access_token',
        'canva_refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'profile_completed_at' => 'datetime',
            'role' => 'integer',
            'status' => 'integer',
            'created_by' => 'integer',
            'password' => 'hashed',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'location_updated_at' => 'datetime',
            'canva_token_expires_at' => 'datetime',
            'can_schedule_posts' => 'boolean',
            'can_use_festival_studio' => 'boolean',
            'can_use_auto_dm' => 'boolean',
            'can_use_ai' => 'boolean',
            'ai_token_quota' => 'integer',
            'ai_tokens_used' => 'integer',
            'ai_quota_resets_at' => 'datetime',
            'can_manage_users' => 'boolean',
            'can_manage_content' => 'boolean',
            'can_manage_tickets' => 'boolean',
            'can_access_system' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public static function getRoleList(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_USER => 'User',
        ];
    }

    public static function getClientTypeList(): array
    {
        return [
            self::CLIENT_TYPE_POLITICIAN => 'Politician / Leader',
            self::CLIENT_TYPE_BUSINESS => 'Business / Enterprise',
            self::CLIENT_TYPE_PERSONAL => 'Creator / Personal',
        ];
    }

    public function getClientTypeNameAttribute(): string
    {
        if (empty($this->client_type)) {
            return 'N/A';
        }

        return self::getClientTypeList()[$this->client_type] ?? ucfirst($this->client_type);
    }

    public function getClientTypeBadgeColorAttribute(): string
    {
        return match ($this->client_type) {
            self::CLIENT_TYPE_POLITICIAN => 'orange',
            self::CLIENT_TYPE_BUSINESS => 'teal',
            self::CLIENT_TYPE_PERSONAL => 'purple',
            default => 'secondary',
        };
    }

    public function getRoleNameAttribute(): string
    {
        return self::getRoleList()[$this->role] ?? 'Unknown';
    }

    public function getRoleColorAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'purple',
            self::ROLE_MANAGER => 'azure',
            default => 'blue',
        };
    }

    public static function getStatusList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_BLOCKED => 'Blocked',
            self::STATUS_SUSPENDED => 'Suspended',
        ];
    }

    public function getStatusNameAttribute(): string
    {
        return self::getStatusList()[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_BLOCKED => 'danger',
            self::STATUS_SUSPENDED => 'warning',
            default => 'secondary',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'ti ti-check',
            self::STATUS_BLOCKED => 'ti ti-ban',
            self::STATUS_SUSPENDED => 'ti ti-alert-triangle',
            default => 'ti ti-info-circle',
        };
    }

    public function getAvatarHtmlAttribute(): string
    {
        if (!empty($this->profile_photo)) {
            $url = filter_var($this->profile_photo, FILTER_VALIDATE_URL)
                ? $this->profile_photo
                : asset('storage/' . $this->profile_photo);
            return '<span class="avatar" style="background-image: url(' . e($url) . ')"></span>';
        }

        $initial = mb_strtoupper(mb_substr($this->name ?? 'U', 0, 1));
        $colors = ['blue', 'green', 'cyan', 'yellow', 'red'];
        $index = abs(crc32($this->name ?? 'U')) % count($colors);
        $colorClass = $colors[$index];

        return '<span class="avatar bg-' . $colorClass . '-lt fw-bold">' . e($initial) . '</span>';
    }

    // ─── Helpers ──────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // ─── Scopes ───────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    public function scopeManager(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_MANAGER);
    }

    public function scopeUsers(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_USER);
    }

    // ─── Relationships ────────────────────────

    public function accounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function autoDmRules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AutoDmRule::class);
    }

    public function brandings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserBranding::class);
    }

    public function defaultBranding(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserBranding::class)->where('is_default', true);
    }

    public function brandingImages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BrandingImage::class);
    }

    public function leaderStrips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LeaderStrip::class);
    }

    public function defaultLeaderStrip(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LeaderStrip::class)->where('is_default', true);
    }

    // ─── Plan Features & Token Quota Helpers ────────────────

    public function canSchedulePosts(): bool
    {
        if ($this->isAdmin() || $this->isManager()) {
            return true;
        }

        return (bool) ($this->can_schedule_posts ?? true);
    }

    public function canUseFestivalStudio(): bool
    {
        if ($this->isAdmin() || $this->isManager()) {
            return true;
        }

        return (bool) ($this->can_use_festival_studio ?? true);
    }

    public function canUseAutoDm(): bool
    {
        if ($this->isAdmin() || $this->isManager()) {
            return true;
        }

        return (bool) ($this->can_use_auto_dm ?? true);
    }

    public function canUseAi(): bool
    {
        if ($this->isAdmin() || $this->isManager()) {
            return true;
        }

        return (bool) ($this->can_use_ai ?? true);
    }

    // ─── Manager Administrative Permission Helpers ──────────

    public function canManageUsers(): bool
    {
        return $this->isAdmin() || ($this->isManager() && (bool) ($this->can_manage_users ?? false));
    }

    public function canManageContent(): bool
    {
        return $this->isAdmin() || ($this->isManager() && (bool) ($this->can_manage_content ?? true));
    }

    public function canManageTickets(): bool
    {
        return $this->isAdmin() || ($this->isManager() && (bool) ($this->can_manage_tickets ?? false));
    }

    public function canAccessSystem(): bool
    {
        return $this->isAdmin() || ($this->isManager() && (bool) ($this->can_access_system ?? false));
    }

    public function hasAnyManagerPermission(): bool
    {
        return $this->canManageUsers() || $this->canManageContent() || $this->canManageTickets() || $this->canAccessSystem();
    }

    /**
     * Check if user has exceeded their monthly AI token quota.
     * Includes self-healing auto-reset when monthly cycle lapses.
     */
    public function hasExceededAiQuota(): bool
    {
        if ($this->isAdmin() || $this->isManager()) {
            return false;
        }

        if (!$this->canUseAi()) {
            return true;
        }

        // Unlimited quota if null
        if ($this->ai_token_quota === null) {
            return false;
        }

        // Lazy Monthly Reset: If billing reset date has passed, reset usage to 0 and push next reset
        if ($this->ai_quota_resets_at && now()->greaterThanOrEqualTo($this->ai_quota_resets_at)) {
            $this->update([
                'ai_tokens_used'     => 0,
                'ai_quota_resets_at' => now()->addMonth()->startOfMonth(),
            ]);
            return false;
        }

        return (int) $this->ai_tokens_used >= (int) $this->ai_token_quota;
    }

    /**
     * Record AI tokens consumed by this user.
     */
    public function recordAiTokenUsage(int $tokens): void
    {
        if ($tokens <= 0) {
            return;
        }

        // Check if cycle reset is due before incrementing
        if ($this->ai_quota_resets_at && now()->greaterThanOrEqualTo($this->ai_quota_resets_at)) {
            $this->update([
                'ai_tokens_used'     => 0,
                'ai_quota_resets_at' => now()->addMonth()->startOfMonth(),
            ]);
        }

        $this->increment('ai_tokens_used', $tokens);

        if ($this->ai_quota_resets_at === null) {
            $this->update([
                'ai_quota_resets_at' => now()->addMonth()->startOfMonth(),
            ]);
        }
    }

    /**
     * Calculate percentage of monthly AI quota consumed.
     */
    public function aiQuotaPercentage(): float
    {
        if (!$this->ai_token_quota || $this->ai_token_quota <= 0) {
            return 0.0;
        }

        return (float) min(100.0, round(($this->ai_tokens_used / $this->ai_token_quota) * 100, 1));
    }

    /**
     * Estimated cost spent on AI tokens (in INR, based on DeepSeek/Gemini average ~ ₹12/M tokens)
     */
    public function estimatedAiCostInr(): float
    {
        if (!$this->ai_tokens_used || $this->ai_tokens_used <= 0) {
            return 0.0;
        }

        // ~ ₹12 per 1,000,000 tokens (1 token = ₹0.000012)
        return round(($this->ai_tokens_used / 1000000) * 12, 2);
    }

    // ─── API Response ────────────────────────

    public function toApiResponse(): array
    {
        return [
            'id' => $this->id,
            'is_profile_complete' => !is_null($this->profile_completed_at),
            'profile_completed_at' => $this->profile_completed_at,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'role' => $this->role,
            'role_name' => $this->role_name,
            'status' => $this->status,
            'status_name' => $this->status_name,
            'profile_photo' => $this->profile_photo ? asset('storage/' . $this->profile_photo) : null,
            'address' => $this->address,
            'state' => $this->state,
            'city' => $this->city,
            'pincode' => $this->pincode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at,
        ];
    }
}
