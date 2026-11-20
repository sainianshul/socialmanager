<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class AutoDmRecipient extends Model
{
    protected $fillable = [
        'account_id',
        'commenter_id',
        'last_dm_sent_at',
        'dm_count',
    ];

    protected $casts = [
        'last_dm_sent_at' => 'datetime',
        'dm_count'        => 'integer',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Check if a DM was sent to this commenter within the cooldown window (default 24 hours).
     */
    public static function isCapped(int $accountId, string $commenterId, int $cooldownHours = 24): bool
    {
        if (empty(trim($commenterId))) {
            return false;
        }

        $record = static::where('account_id', $accountId)
            ->where('commenter_id', trim($commenterId))
            ->first();

        if (!$record || !$record->last_dm_sent_at) {
            return false;
        }

        return $record->last_dm_sent_at->greaterThan(now()->subHours($cooldownHours));
    }

    /**
     * Record a successfully dispatched Auto-DM for frequency tracking.
     */
    public static function recordDispatch(int $accountId, string $commenterId): self
    {
        $cleanId = trim($commenterId);

        $record = static::where('account_id', $accountId)
            ->where('commenter_id', $cleanId)
            ->first();

        if ($record) {
            $record->update([
                'last_dm_sent_at' => now(),
                'dm_count'        => $record->dm_count + 1,
            ]);
            return $record;
        }

        return static::create([
            'account_id'      => $accountId,
            'commenter_id'    => $cleanId,
            'last_dm_sent_at' => now(),
            'dm_count'        => 1,
        ]);
    }
}
