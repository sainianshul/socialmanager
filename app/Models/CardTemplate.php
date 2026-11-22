<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardTemplate extends Model
{
    use HasFactory;

    protected $table = 'card_templates';

    protected $fillable = [
        'party_id',
        'title',
        'category',
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

    public static function categories(): array
    {
        return [
            'birthday' => 'Birthday (जन्मदिन)',
            'jayanti' => 'Jayanti (जयंती)',
            'punyatithi' => 'Punyatithi (पुण्यतिथि / श्रद्धांजलि)',
            'congratulations' => 'Congratulations (बधाई / शुभकामनाएं)',
            'daily_quote' => 'Daily Quotes (सुविचार)',
            'campaign' => 'Election Campaign (चुनावी प्रचार)',
            'announcement' => 'Announcement (सार्वजनिक घोषणा)',
            'general' => 'General (सामान्य)',
        ];
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
        $cleanPath = ltrim($this->template_image_path, '/\\');
        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }
        return asset('storage/' . $cleanPath);
    }
}
