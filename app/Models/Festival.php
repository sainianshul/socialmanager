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
}
