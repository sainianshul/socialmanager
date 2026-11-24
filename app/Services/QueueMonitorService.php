<?php

namespace App\Services;

use App\Models\SystemActivityLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;

class QueueMonitorService
{
    /**
     * Get queue statistics and health indicators.
     */
    public function getStats(): array
    {
        $driver = config('queue.default', 'sync');
        $connectionStatus = 'offline';
        $pendingJobs = 0;
        $failedJobs = 0;
        $activeQueues = ['default'];

        // 1. Check failed jobs count safely
        try {
            if (DB::getSchemaBuilder()->hasTable('failed_jobs')) {
                $failedJobs = DB::table('failed_jobs')->count();
            }
        } catch (\Throwable $e) {
            Log::debug("QueueMonitorService failed_jobs check: " . $e->getMessage());
        }

        // 2. Pending jobs count & connection status
        try {
            if ($driver === 'redis') {
                if (class_exists('Redis')) {
                    $redisConn = Redis::connection();
                    $ping = $redisConn->ping();
                    if ($ping) {
                        $connectionStatus = 'online';
                        foreach (['default', 'notifications', 'high'] as $q) {
                            $size = (int) $redisConn->llen("queues:{$q}");
                            $delayed = (int) $redisConn->zcard("queues:{$q}:delayed");
                            $reserved = (int) $redisConn->zcard("queues:{$q}:reserved");
                            $totalForQ = $size + $delayed + $reserved;
                            if ($totalForQ > 0 || $q === 'default') {
                                $activeQueues[] = $q;
                            }
                            $pendingJobs += $totalForQ;
                        }
                        $activeQueues = array_values(array_unique($activeQueues));
                    }
                } else {
                    $connectionStatus = 'extension_missing';
                }
            } elseif ($driver === 'database') {
                if (DB::getSchemaBuilder()->hasTable('jobs')) {
                    $connectionStatus = 'online';
                    $pendingJobs = DB::table('jobs')->count();
                    $queues = DB::table('jobs')->distinct()->pluck('queue')->toArray();
                    if (!empty($queues)) {
                        $activeQueues = $queues;
                    }
                }
            } elseif ($driver === 'sync') {
                $connectionStatus = 'sync_mode';
                $pendingJobs = 0;
            }
        } catch (\Throwable $e) {
            $connectionStatus = 'error: ' . $e->getMessage();
            Log::debug("QueueMonitorService queue size check: " . $e->getMessage());
        }

        // 3. Activity events today
        $activityToday = 0;
        $syncToday = 0;
        try {
            if (DB::getSchemaBuilder()->hasTable('system_activity_logs')) {
                $activityToday = SystemActivityLog::whereDate('created_at', now()->today())->count();
                $syncToday = SystemActivityLog::whereDate('created_at', now()->today())
                    ->where('event_type', SystemActivityLog::EVENT_ACCOUNT_SYNC)
                    ->count();
            }
        } catch (\Throwable $e) {
            Log::debug("QueueMonitorService activity stats check: " . $e->getMessage());
        }

        return [
            'driver'            => $driver,
            'connection_status' => $connectionStatus,
            'pending_jobs'      => $pendingJobs,
            'failed_jobs'       => $failedJobs,
            'activity_today'    => $activityToday,
            'sync_today'        => $syncToday,
            'active_queues'     => $activeQueues,
        ];
    }

    /**
     * Get paginated failed jobs with decoded payload details.
     */
    public function getFailedJobs(int $perPage = 15): LengthAwarePaginator
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('failed_jobs')) {
                return new LengthAwarePaginator([], 0, $perPage);
            }

            $paginated = DB::table('failed_jobs')
                ->orderByDesc('id')
                ->paginate($perPage, ['*'], 'failed_page');

            $paginated->getCollection()->transform(function ($job) {
                $payload = json_decode($job->payload, true) ?: [];
                $displayName = $payload['displayName'] ?? ($payload['job'] ?? 'Unknown Job');

                // Extract clean exception headline
                $exceptionLines = explode("\n", $job->exception ?? '');
                $headline = $exceptionLines[0] ?? 'Unknown error';

                $job->display_name = $displayName;
                $job->payload_decoded = $payload;
                $job->exception_headline = $headline;
                return $job;
            });

            return $paginated;
        } catch (\Throwable $e) {
            Log::error("Failed to load failed jobs: " . $e->getMessage());
            return new LengthAwarePaginator([], 0, $perPage);
        }
    }

    /**
     * Retry a specific failed job by its ID or UUID.
     */
    public function retryJob(string|int $id): array
    {
        try {
            Artisan::call('queue:retry', ['id' => [(string) $id]]);
            $output = trim(Artisan::output());

            ActivityLogger::log(
                SystemActivityLog::EVENT_QUEUE_JOB,
                "Retried failed queue job #{$id}",
                SystemActivityLog::STATUS_SUCCESS,
                ['job_id' => $id],
                $output
            );

            return ['success' => true, 'message' => "Job #{$id} queued for retry."];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Failed to retry job: ' . $e->getMessage()];
        }
    }

    /**
     * Retry all failed jobs.
     */
    public function retryAll(): array
    {
        try {
            Artisan::call('queue:retry', ['id' => ['all']]);
            $output = trim(Artisan::output());

            ActivityLogger::log(
                SystemActivityLog::EVENT_QUEUE_JOB,
                "Retried ALL failed queue jobs",
                SystemActivityLog::STATUS_SUCCESS,
                null,
                $output
            );

            return ['success' => true, 'message' => 'All failed jobs have been pushed back to the queue.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Failed to retry jobs: ' . $e->getMessage()];
        }
    }

    /**
     * Delete/Forget a specific failed job.
     */
    public function deleteFailedJob(string|int $id): array
    {
        try {
            Artisan::call('queue:forget', ['id' => (string) $id]);

            ActivityLogger::log(
                SystemActivityLog::EVENT_QUEUE_JOB,
                "Purged failed queue job #{$id}",
                SystemActivityLog::STATUS_SUCCESS,
                ['job_id' => $id]
            );

            return ['success' => true, 'message' => "Failed job #{$id} deleted."];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Failed to delete job: ' . $e->getMessage()];
        }
    }

    /**
     * Clear / flush all failed jobs.
     */
    public function clearAllFailed(): array
    {
        try {
            Artisan::call('queue:flush');

            ActivityLogger::log(
                SystemActivityLog::EVENT_QUEUE_JOB,
                "Flushed all failed queue jobs",
                SystemActivityLog::STATUS_SUCCESS
            );

            return ['success' => true, 'message' => 'All failed jobs cleared from the database.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Failed to flush jobs: ' . $e->getMessage()];
        }
    }
}
