<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaderStrip extends Model
{
    use HasFactory;

    protected $table = 'leader_strips';

    protected $fillable = [
        'user_id',
        'party_id',
        'name',
        'is_default',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    /**
     * Resolve the effective leadership strip according to user preference and party default.
     * Priority:
     * 1. Explicitly requested strip ID (if provided)
     * 2. User's custom strip (if user added custom leaders/strips)
     * 3. Target party's official default strip (matching template party or user branding party)
     * 4. System default fallback strip
     */
    public static function resolveFor(?User $user, mixed $template = null, ?UserBranding $branding = null, ?int $stripId = null): ?self
    {
        // 1. Explicitly requested strip ID
        if (!empty($stripId)) {
            $explicit = self::with('leaders')->find($stripId);
            if ($explicit && $explicit->leaders->isNotEmpty()) {
                return $explicit;
            }
        }

        $templatePartyId = ($template instanceof FestivalTemplate) ? $template->party_id : null;

        // 2. If user has created custom strip(s), use user's custom strip
        if ($user) {
            // First check party-specific custom strip if template has a specific party
            if ($templatePartyId) {
                $userPartyStrip = $user->leaderStrips()
                    ->where('party_id', $templatePartyId)
                    ->orderByDesc('is_default')
                    ->with('leaders')
                    ->first();
                if ($userPartyStrip && $userPartyStrip->leaders->isNotEmpty()) {
                    return $userPartyStrip;
                }
            }

            // User's default custom strip
            $userDefault = $user->defaultLeaderStrip()->with('leaders')->first();
            if ($userDefault && $userDefault->leaders->isNotEmpty()) {
                return $userDefault;
            }

            // Any custom strip belonging to the user
            $userFirst = $user->leaderStrips()->orderByDesc('is_default')->with('leaders')->first();
            if ($userFirst && $userFirst->leaders->isNotEmpty()) {
                return $userFirst;
            }
        }

        // 3. Fallback to Party Official Default Strip
        $targetPartyId = $templatePartyId ?: ($branding?->party_id ?? $user?->defaultBranding?->party_id);
        if ($targetPartyId) {
            $partyStrip = self::where('party_id', $targetPartyId)
                ->where('is_system', true)
                ->where('is_default', true)
                ->with('leaders')
                ->first()
                ?? self::where('party_id', $targetPartyId)
                    ->where('is_system', true)
                    ->with('leaders')
                    ->first()
                ?? self::where('party_id', $targetPartyId)
                    ->with('leaders')
                    ->first();

            if ($partyStrip && $partyStrip->leaders->isNotEmpty()) {
                return $partyStrip;
            }
        }

        // 4. System Default Fallback Strip
        return self::where('is_system', true)
            ->where('is_default', true)
            ->with('leaders')
            ->first()
            ?? self::where('is_system', true)
                ->with('leaders')
                ->first();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(PoliticalParty::class, 'party_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LeaderStripItem::class, 'leader_strip_id')->orderBy('order', 'asc');
    }

    public function leaders(): BelongsToMany
    {
        return $this->belongsToMany(PoliticalLeader::class, 'leader_strip_items', 'leader_strip_id', 'political_leader_id')
                    ->withPivot('order')
                    ->withTimestamps()
                    ->orderByPivot('order', 'asc');
    }
}
