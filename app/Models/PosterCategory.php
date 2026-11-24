<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosterCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'show_as_collection',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'show_as_collection' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Posters belonging to this category.
     */
    public function posters(): HasMany
    {
        return $this->hasMany(Poster::class, 'category_id')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Scope to active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Scope to collections shown on top.
     */
    public function scopeCollections($query)
    {
        return $query->where('show_as_collection', true)->where('is_active', true);
    }
}
