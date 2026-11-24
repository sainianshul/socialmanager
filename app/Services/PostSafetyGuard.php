<?php

namespace App\Services;

use App\Models\ScheduledPost;
use Carbon\Carbon;

class PostSafetyGuard
{
    /**
     * Risky URL shorteners & file locker domains known to trigger platform spam/phishing blocks
     */
    public const BLOCKED_DOMAINS = [
        'bit.ly',
        'tinyurl.com',
        'tiny.cc',
        'cutt.ly',
        'mega.nz',
        't.co',
        'goo.gl',
        'ow.ly',
        'is.gd',
        'buff.ly',
        'adf.ly',
        'shorturl.at',
        'rebrand.ly',
        'bl.ink',
        'trib.al',
    ];

    public const MAX_HASHTAGS = 20;
    public const MAX_DAILY_POSTS = 20;
    public const COLLISION_GAP_MINUTES = 10;

    /**
     * Check if text contains any blocked URL shortener or suspicious domains
     */
    public static function checkBlockedDomains(?string ...$texts): ?string
    {
        $escaped = array_map(function ($domain) {
            return preg_quote($domain, '/');
        }, self::BLOCKED_DOMAINS);

        $pattern = '/(?:https?:\/\/)?(?:www\.)?(' . implode('|', $escaped) . ')(?:[\/?#]|\b)/i';

        foreach ($texts as $text) {
            if (empty($text)) {
                continue;
            }
            if (preg_match($pattern, $text, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Count total hashtags in text (supporting English, digits, and Unicode/Hindi characters)
     */
    public static function countHashtags(?string ...$texts): int
    {
        $count = 0;
        foreach ($texts as $text) {
            if (empty($text)) {
                continue;
            }
            preg_match_all('/#([\p{L}\p{N}_]+)/u', $text, $matches);
            if (!empty($matches[0])) {
                $count += count($matches[0]);
            }
        }
        return $count;
    }

    /**
     * Check if an account has reached its daily limit of 20 posts for the given calendar day
     */
    public static function checkDailyLimit(int $accountId, Carbon $scheduledAt, ?int $ignorePostId = null): ?int
    {
        $dayStart = $scheduledAt->copy()->startOfDay();
        $dayEnd = $scheduledAt->copy()->endOfDay();

        $query = ScheduledPost::where('account_id', $accountId)
            ->whereBetween('scheduled_at', [$dayStart, $dayEnd])
            ->whereIn('status', [
                ScheduledPost::STATUS_SCHEDULED,
                ScheduledPost::STATUS_PUBLISHED,
                ScheduledPost::STATUS_PUBLISHING,
            ]);

        if ($ignorePostId) {
            $query->where('id', '!=', $ignorePostId);
        }

        $count = $query->count();
        return $count >= self::MAX_DAILY_POSTS ? $count : null;
    }

    /**
     * Check if another post is already scheduled within the collision window (10 minutes) on this account
     */
    public static function checkCollision(int $accountId, Carbon $scheduledAt, ?int $ignorePostId = null): ?ScheduledPost
    {
        $windowStart = $scheduledAt->copy()->subMinutes(self::COLLISION_GAP_MINUTES);
        $windowEnd = $scheduledAt->copy()->addMinutes(self::COLLISION_GAP_MINUTES);

        $query = ScheduledPost::where('account_id', $accountId)
            ->whereBetween('scheduled_at', [$windowStart, $windowEnd])
            ->whereIn('status', [
                ScheduledPost::STATUS_SCHEDULED,
                ScheduledPost::STATUS_PUBLISHING,
            ]);

        if ($ignorePostId) {
            $query->where('id', '!=', $ignorePostId);
        }

        return $query->first();
    }
}
