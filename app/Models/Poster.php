<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Poster extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'party_id',
        'name',
        'slug',
        'image_path',
        'thumbnail_path',
        'default_caption',
        'show_on_top',
        'auto_show_date',
        'usage_count',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'show_on_top' => 'boolean',
        'auto_show_date' => 'date',
        'usage_count' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
        'thumbnail_url',
    ];

    /**
     * Category relation.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PosterCategory::class, 'category_id');
    }

    /**
     * Political party relation (nullable).
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(PoliticalParty::class, 'party_id');
    }

    /**
     * Full URL of the poster image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return '';
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return Storage::url($this->image_path);
    }

    /**
     * Fast thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): string
    {
        $path = $this->thumbnail_path ?: $this->image_path;

        if (empty($path)) {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::url($path);
    }

    /**
     * Scope active posters.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope posters for a party or universal.
     */
    public function scopeForParty($query, $partyId = null)
    {
        return $query->where(function ($q) use ($partyId) {
            $q->whereNull('party_id');
            if ($partyId) {
                $q->orWhere('party_id', $partyId);
            }
        });
    }

    /**
     * Scope trending / most used.
     */
    public function scopeTrending($query)
    {
        return $query->orderByDesc('usage_count');
    }
}
