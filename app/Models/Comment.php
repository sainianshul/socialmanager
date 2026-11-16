<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    // Workflow Status Constants
    public const STATUS_NEW            = 0;
    public const STATUS_PENDING_REVIEW = 1;
    public const STATUS_REPLIED        = 2;
    public const STATUS_HIDDEN         = 3;
    public const STATUS_FLAGGED        = 4;
    public const STATUS_IGNORED        = 5;

    protected $fillable = [
        'post_id',
        'account_id',
        'meta_comment_id',
        'platform_comment_id',
        'parent_comment_id',
        'commenter_name',
        'commenter_meta_id',
        'commenter_photo_url',
        'message',
        'sentiment',
        'sentiment_score',
        'category',
        'likes_count',
        'is_abusive',
        'abuse_confidence',
        'is_favor',
        'is_against',
        'is_complaint',
        'should_hide',
        'reply_needed',
        'status',
        'ai_reply',
        'meta_reply_id',
        'replied_at',
        'hidden_at',
        'commented_at',
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'sentiment_score' => 'decimal:2',
        'abuse_confidence' => 'decimal:2',
        'is_abusive' => 'boolean',
        'is_favor' => 'boolean',
        'is_against' => 'boolean',
        'is_complaint' => 'boolean',
        'should_hide' => 'boolean',
        'reply_needed' => 'boolean',
        'status' => 'integer',
        'replied_at' => 'datetime',
        'hidden_at' => 'datetime',
        'commented_at' => 'datetime',
    ];

    /**
     * Post this comment belongs to
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Account this comment belongs to
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Parent comment if this is a reply
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }

    /**
     * Nested child replies
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }

    /**
     * Status label mapping
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_NEW            => 'New',
            self::STATUS_PENDING_REVIEW => 'Pending Review',
            self::STATUS_REPLIED        => 'Replied',
            self::STATUS_HIDDEN         => 'Hidden',
            self::STATUS_FLAGGED        => 'Flagged',
            self::STATUS_IGNORED        => 'Ignored',
        ];
    }

    public function getStatusNameAttribute(): string
    {
        return self::getStatusList()[$this->status] ?? 'Unknown';
    }

    /**
     * Scopes for platform filtering
     */
    public function scopeInstagram($query)
    {
        return $query->whereHas('account', fn($q) => $q->where('account_type', Account::TYPE_INSTAGRAM));
    }

    public function scopeFacebook($query)
    {
        return $query->whereHas('account', fn($q) => $q->where('account_type', Account::TYPE_FACEBOOK));
    }

    public function scopeYouTube($query)
    {
        return $query->whereHas('account', fn($q) => $q->where('account_type', Account::TYPE_YOUTUBE));
    }

    /**
     * Scopes for moderation status
     */
    public function scopePendingReview($query)
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_PENDING_REVIEW]);
    }

    public function scopeReplied($query)
    {
        return $query->where('status', self::STATUS_REPLIED);
    }

    public function scopeHidden($query)
    {
        return $query->where('status', self::STATUS_HIDDEN);
    }

    public function scopeFlagged($query)
    {
        return $query->where('status', self::STATUS_FLAGGED);
    }

    /**
     * Unified external comment identifier
     */
    public function getEffectiveCommentIdAttribute(): ?string
    {
        return $this->platform_comment_id ?: $this->meta_comment_id;
    }
}

