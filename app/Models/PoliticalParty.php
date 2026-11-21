<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoliticalParty extends Model
{
    use HasFactory;

    protected $table = 'political_parties';

    protected $fillable = [
        'name',
        'short_code',
        'symbol_logo',
        'top_leaders_strip',
        'group_cutout_image',
        'primary_color',
        'secondary_color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function userBrandings(): HasMany
    {
        return $this->hasMany(UserBranding::class, 'party_id');
    }

    public function templates(): HasMany
    {
        return $this->hasMany(FestivalTemplate::class, 'party_id');
    }

    public function leaders(): HasMany
    {
        return $this->hasMany(PoliticalLeader::class, 'party_id')->where('is_active', true);
    }

    public function strips(): HasMany
    {
        return $this->hasMany(LeaderStrip::class, 'party_id');
    }

    public function defaultStrip()
    {
        return $this->hasOne(LeaderStrip::class, 'party_id')->where('is_system', true)->where('is_default', true);
    }

    public function getSymbolLogoUrlAttribute(): ?string
    {
        if (empty($this->symbol_logo)) {
            return null;
        }
        return filter_var($this->symbol_logo, FILTER_VALIDATE_URL)
            ? $this->symbol_logo
            : asset('storage/' . $this->symbol_logo);
    }

    public function getTopLeadersStripUrlAttribute(): ?string
    {
        if (empty($this->top_leaders_strip)) {
            return null;
        }
        return filter_var($this->top_leaders_strip, FILTER_VALIDATE_URL)
            ? $this->top_leaders_strip
            : asset('storage/' . $this->top_leaders_strip);
    }

    public function getGroupCutoutUrlAttribute(): ?string
    {
        if (empty($this->group_cutout_image)) {
            return null;
        }
        if (filter_var($this->group_cutout_image, FILTER_VALIDATE_URL)) {
            return $this->group_cutout_image;
        }
        $cleanPath = ltrim($this->group_cutout_image, '/\\');
        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }
        return asset('storage/' . $this->group_cutout_image);
    }
}
