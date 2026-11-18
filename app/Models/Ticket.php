<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    const CATEGORY_TECHNICAL = 'technical';
    const CATEGORY_ACCOUNT_SYNC = 'account_sync';
    const CATEGORY_AI_AUTOMATION = 'ai_automation';
    const CATEGORY_BILLING = 'billing';
    const CATEGORY_FEATURE_REQUEST = 'feature_request';
    const CATEGORY_GENERAL = 'general';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category',
        'title',
        'description',
        'priority',
        'status',
        'admin_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public static function getStatusList(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    public static function getCategoryList(): array
    {
        return [
            self::CATEGORY_TECHNICAL => 'Technical Issue',
            self::CATEGORY_ACCOUNT_SYNC => 'Social Account & Sync',
            self::CATEGORY_AI_AUTOMATION => 'AI Moderation & Replies',
            self::CATEGORY_BILLING => 'Billing & Subscription',
            self::CATEGORY_FEATURE_REQUEST => 'Feature Request',
            self::CATEGORY_GENERAL => 'General Inquiry',
        ];
    }

    public static function getPriorityList(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    public function getCategoryNameAttribute(): string
    {
        return self::getCategoryList()[$this->category] ?? ucfirst($this->category);
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_TECHNICAL => 'azure',
            self::CATEGORY_ACCOUNT_SYNC => 'pink',
            self::CATEGORY_AI_AUTOMATION => 'teal',
            self::CATEGORY_BILLING => 'warning',
            self::CATEGORY_FEATURE_REQUEST => 'purple',
            default => 'secondary',
        };
    }

    public function getStatusNameAttribute(): string
    {
        return self::getStatusList()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'primary',
            self::STATUS_IN_PROGRESS => 'warning',
            self::STATUS_RESOLVED => 'success',
            self::STATUS_CLOSED => 'secondary',
            default => 'info',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'secondary',
            self::PRIORITY_MEDIUM => 'info',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_URGENT => 'danger',
            default => 'secondary',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'TCK';
        $random = strtoupper(bin2hex(random_bytes(3))); // 6 hex chars
        $number = "{$prefix}-{$random}";

        while (self::where('ticket_number', $number)->exists()) {
            $random = strtoupper(bin2hex(random_bytes(3)));
            $number = "{$prefix}-{$random}";
        }

        return $number;
    }
}
