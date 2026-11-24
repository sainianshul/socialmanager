<?php

namespace App\Services;

use App\Models\SystemActivityLog;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Record a system activity log safely without interrupting app execution.
     */
    public static function log(
        string $eventType,
        string $action,
        string $status = SystemActivityLog::STATUS_SUCCESS,
        mixed $payload = null,
        mixed $response = null,
        ?int $durationMs = null,
        ?int $accountId = null,
        ?int $userId = null
    ): ?SystemActivityLog {
        try {
            $userId = $userId ?? (auth()->check() ? auth()->id() : null);

            // Auto-resolve user_id from account if available and not explicitly provided
            if (!$userId && $accountId) {
                try {
                    $userId = \App\Models\Account::where('id', $accountId)->value('user_id');
                } catch (\Throwable $lookupErr) {
                    // Safe fallback
                }
            }

            // Prepare payload safely
            $cleanPayload = is_array($payload) ? $payload : (is_string($payload) ? ['data' => $payload] : null);

            // Prepare response safely
            $cleanResponse = is_string($response) ? $response : (is_null($response) ? null : json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            // Truncate response if too large (> 64KB)
            if ($cleanResponse && strlen($cleanResponse) > 65535) {
                $cleanResponse = substr($cleanResponse, 0, 65500) . "\n... [truncated for database safety]";
            }

            $ip = null;
            try {
                $ip = request()?->ip();
            } catch (\Throwable $ipErr) {
                // Ignore if running in console
            }

            return SystemActivityLog::create([
                'user_id'     => $userId,
                'account_id'  => $accountId,
                'event_type'  => $eventType,
                'action'      => substr($action, 0, 190),
                'status'      => $status,
                'payload'     => $cleanPayload,
                'response'    => $cleanResponse,
                'duration_ms' => $durationMs,
                'ip_address'  => $ip,
            ]);
        } catch (\Throwable $e) {
            // Failsafe: logging should never crash the main application workflow
            Log::warning("ActivityLogger error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper for successful event log
     */
    public static function success(
        string $eventType,
        string $action,
        mixed $payload = null,
        mixed $response = null,
        ?int $durationMs = null,
        ?int $accountId = null,
        ?int $userId = null
    ): ?SystemActivityLog {
        return self::log($eventType, $action, SystemActivityLog::STATUS_SUCCESS, $payload, $response, $durationMs, $accountId, $userId);
    }

    /**
     * Helper for failed event log
     */
    public static function failed(
        string $eventType,
        string $action,
        mixed $error = null,
        mixed $payload = null,
        ?int $durationMs = null,
        ?int $accountId = null,
        ?int $userId = null
    ): ?SystemActivityLog {
        $errorMessage = $error instanceof \Throwable
            ? ($error->getMessage() . "\n" . $error->getFile() . ':' . $error->getLine())
            : (is_string($error) ? $error : json_encode($error));

        return self::log($eventType, $action, SystemActivityLog::STATUS_FAILED, $payload, $errorMessage, $durationMs, $accountId, $userId);
    }

    /**
     * Prune logs older than $days days to maintain high database performance.
     */
    public static function prune(int $days = 30): int
    {
        try {
            return SystemActivityLog::where('created_at', '<', now()->subDays($days))->delete();
        } catch (\Throwable $e) {
            Log::warning("ActivityLogger prune error: " . $e->getMessage());
            return 0;
        }
    }
}
