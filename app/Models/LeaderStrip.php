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
