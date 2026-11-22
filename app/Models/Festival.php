<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Festival extends Model
{
    use HasFactory;

    protected $table = 'festivals';

    protected $fillable = [
        'title',
        'festival_date',
        'category',
        'ready_caption',
        'hashtags',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'festival_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function templates(): HasMany
    {
        return $this->hasMany(FestivalTemplate::class, 'festival_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('festival_date', '>=', now()->toDateString())
            ->orderBy('festival_date', 'asc');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'religious_festival' => 'Religious Festival',
            'national_day' => 'National Day',
            'jayanti_punyatithi' => 'Jayanti / Memorial',
            'international_day' => 'International Day',
            default => 'General',
        };
    }

    public function getCategoryBadgeColorAttribute(): string
    {
        return match ($this->category) {
            'religious_festival' => 'text-primary',
            'national_day' => 'text-success',
            'jayanti_punyatithi' => 'text-warning',
            'international_day' => 'text-azure',
            default => 'text-secondary',
        };
    }
}
