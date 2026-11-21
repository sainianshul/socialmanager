<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandingImage extends Model
{
    use HasFactory;

    protected $table = 'branding_images';

    protected $fillable = [
        'user_id',
        'user_branding_id',
        'title',
        'original_image_path',
        'cutout_image_path',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branding(): BelongsTo
    {
        return $this->belongsTo(UserBranding::class, 'user_branding_id');
    }

    public function getCutoutUrlAttribute(): ?string
    {
        $path = $this->cutout_image_path ?: $this->original_image_path;
        if (empty($path)) {
            return null;
        }
        return filter_var($path, FILTER_VALIDATE_URL)
            ? $path
            : asset('storage/' . $path);
    }
}
