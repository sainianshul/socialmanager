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
        'preset_key',
        'layout_config',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'layout_config' => 'array',
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
            return asset('images/festival-templates-sample.jpg');
        }
        if (filter_var($this->template_image_path, FILTER_VALIDATE_URL)) {
            return $this->template_image_path;
        }
        if (str_starts_with($this->template_image_path, 'images/')) {
            return asset($this->template_image_path);
        }
        return asset('storage/' . $this->template_image_path);
    }
}
