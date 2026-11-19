<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoDmRule extends Model
{
    use HasFactory;

    public const MATCH_CONTAINS = 'contains';
    public const MATCH_EXACT = 'exact';

    protected $fillable = [
        'user_id',
        'account_id',
        'post_id',
        'name',
        'keywords',
        'match_type',
        'dm_message',
        'public_reply',
        'is_active',
        'trigger_count',
        'last_triggered_at',
    ];

    protected $casts = [
        'keywords' => 'array',
        'is_active' => 'boolean',
        'trigger_count' => 'integer',
        'last_triggered_at' => 'datetime',
    ];

    /**
     * The User who owns this automation rule.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The social account (Facebook Page or Instagram Business) this rule monitors.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * The optional specific post this rule targets (null = applies to all posts on account).
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Scope a query to only include active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if a given comment text matches any of this rule's trigger keywords.
     */
    public function matchesText(string $commentText): bool
    {
        if (empty($this->keywords) || !is_array($this->keywords)) {
            return false;
        }

        $cleanedComment = mb_strtolower(trim($commentText));

        foreach ($this->keywords as $keyword) {
            $keyword = mb_strtolower(trim($keyword));
            if ($keyword === '') {
                continue;
            }

            if ($this->match_type === self::MATCH_EXACT) {
                if ($cleanedComment === $keyword) {
                    return true;
                }
            } else {
                // Default: contains substring or keyword
                if (str_contains($cleanedComment, $keyword)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Increment rule trigger count and record last triggered timestamp.
     */
    public function recordTrigger(): void
    {
        $this->increment('trigger_count');
        $this->forceFill(['last_triggered_at' => now()])->save();
    }
}
