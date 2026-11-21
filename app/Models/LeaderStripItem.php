<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderStripItem extends Model
{
    use HasFactory;

    protected $table = 'leader_strip_items';

    protected $fillable = [
        'leader_strip_id',
        'political_leader_id',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function strip(): BelongsTo
    {
        return $this->belongsTo(LeaderStrip::class, 'leader_strip_id');
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(PoliticalLeader::class, 'political_leader_id');
    }
}
