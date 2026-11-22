<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoliticalLeader extends Model
{
    use HasFactory;

    protected $table = 'political_leaders';

    protected $fillable = [
        'party_id',
        'name',
        'designation',
        'image',
        'state',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(PoliticalParty::class, 'party_id');
    }

    public function stripItems(): HasMany
    {
        return $this->hasMany(LeaderStripItem::class, 'political_leader_id');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            $cleanPath = ltrim($this->image, '/\\');
            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }
            return asset('storage/' . $cleanPath);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=ff9933&color=fff&bold=true&size=120';
    }
}
