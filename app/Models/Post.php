<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'meta_post_id',
        'platform_post_id',
        'title',
        'message',
        'description',
        'media_type',
        'media_url',
        'thumbnail_url',
        'permalink_url',
        'duration',
        'views_count',
        'likes_count',
        'comments_count',
        'shares_count',
        'tags',
        'privacy_status',
        'published_at',
        'last_comment_sync_at',
    ];

    protected $casts = [
        'duration' => 'integer',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'tags' => 'array',
        'published_at' => 'datetime',
        'last_comment_sync_at' => 'datetime',
    ];

    /**
     * Account that owns this post
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Comments on this post
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Display title for YouTube videos or post message fallback
     */
    public function getDisplayTitleAttribute(): string
    {
        if (!empty($this->title)) {
            return $this->title;
        }
        return Str::limit($this->message ?? 'Untitled Media Post', 70);
    }

    /**
     * Formatted duration in MM:SS or HH:MM:SS
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration || $this->duration <= 0) {
            return '0:00';
        }
        if ($this->duration >= 3600) {
            return gmdate('H:i:s', $this->duration);
        }
        return gmdate('i:s', $this->duration);
    }

    /**
     * Formatted views count (e.g. 1.2K, 3.4M)
     */
    public function getFormattedViewsAttribute(): string
    {
        $v = $this->views_count ?? 0;
        if ($v >= 1000000) {
            return round($v / 1000000, 1) . 'M';
        }
        if ($v >= 1000) {
            return round($v / 1000, 1) . 'K';
        }
        return (string) $v;
    }

    /**
     * Platform scopes
     */
    public function scopeFacebook($query)
    {
        return $query->whereHas('account', fn($q) => $q->where('account_type', Account::TYPE_FACEBOOK));
    }

    public function scopeInstagram($query)
    {
        return $query->whereHas('account', fn($q) => $q->where('account_type', Account::TYPE_INSTAGRAM));
    }

    public function scopeYouTube($query)
    {
        return $query->whereHas('account', fn($q) => $q->where('account_type', Account::TYPE_YOUTUBE));
    }

    public function scopeForPlatform($query, ?int $platform)
    {
        if ($platform) {
            return $query->whereHas('account', fn($q) => $q->where('account_type', $platform));
        }
        return $query;
    }
}
