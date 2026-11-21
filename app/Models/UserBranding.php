<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserBranding extends Model
{
    use HasFactory;

    protected $table = 'user_brandings';

    protected $fillable = [
        'user_id',
        'client_type',
        'party_id',
        'display_name',
        'designation',
        'phone_number',
        'social_handle',
        'website',
        'frame_style',
        'include_senior_leaders',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'include_senior_leaders' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
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

    public function images(): HasMany
    {
        return $this->hasMany(BrandingImage::class, 'user_branding_id');
    }

    public function defaultImage()
    {
        return $this->hasOne(BrandingImage::class, 'user_branding_id')->where('is_default', true);
    }
}
