<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FestivalTemplate extends Model
{
    use HasFactory;

    protected $table = 'festival_templates';

    protected $fillable = [
        'festival_id',
        'party_id',
        'title',
        'template_image_path',
        'thumbnail_path',
        'photo_position',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(PoliticalParty::class, 'party_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->template_image_path)) {
            return null;
        }
        return filter_var($this->template_image_path, FILTER_VALIDATE_URL)
            ? $this->template_image_path
            : asset('storage/' . $this->template_image_path);
    }
}
