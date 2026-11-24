<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemActivityLog extends Model
{
    use HasFactory;

    protected $table = 'system_activity_logs';

    protected $fillable = [
        'user_id',
        'account_id',
        'event_type',
        'action',
        'status',
        'payload',
        'response',
        'duration_ms',
        'ip_address',
    ];

    protected $casts = [
        'payload'     => 'array',
        'duration_ms' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    // Status Constants
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED  = 'failed';
    public const STATUS_PENDING = 'pending';

    // Event Type Constants
    public const EVENT_ACCOUNT_SYNC    = 'account_sync';
    public const EVENT_POST_PUBLISH    = 'post_publish';
    public const EVENT_CANVA           = 'canva';
    public const EVENT_AUTO_DM         = 'auto_dm';
    public const EVENT_COMMENT_PROCESS = 'comment_process';
    public const EVENT_WEBHOOK         = 'webhook';
    public const EVENT_QUEUE_JOB       = 'queue_job';

    /**
     * User who initiated or is linked to the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Connected social account linked to this event
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Fast filtering scope for admin dashboard
     */
    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('response', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('account', function ($accQ) use ($search) {
                      $accQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }

        return $query;
    }
}
