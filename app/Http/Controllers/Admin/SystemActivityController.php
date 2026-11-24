<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\SystemActivityLog;
use App\Services\ActivityLogger;
use App\Services\QueueMonitorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemActivityController extends Controller
{
    /**
     * Display live activity logs & queue monitor dashboard.
     */
    public function index(Request $request, QueueMonitorService $queueService): View
    {
        $stats = $queueService->getStats();
        $activeTab = $request->get('tab', 'logs');

        // Paginated System Activity Logs
        $logs = SystemActivityLog::with([
                'user:id,name,email,role',
                'account:id,name,account_type',
            ])
            ->filter($request->only(['status', 'event_type', 'search', 'account_id', 'user_id']))
            ->latest('id')
            ->paginate(20, ['*'], 'page')
            ->withQueryString();

        // Paginated Failed Queue Jobs
        $failedJobs = $queueService->getFailedJobs(15);

        // Filter Dropdown options
        $eventTypes = [
            SystemActivityLog::EVENT_ACCOUNT_SYNC    => 'Account Sync',
            SystemActivityLog::EVENT_POST_PUBLISH    => 'Post & Video Publish',
            SystemActivityLog::EVENT_CANVA           => 'Canva Studio',
            SystemActivityLog::EVENT_AUTO_DM         => 'Auto DM',
            SystemActivityLog::EVENT_COMMENT_PROCESS => 'Comment Process',
            SystemActivityLog::EVENT_AI              => 'AI Generation',
            SystemActivityLog::EVENT_WEBHOOK         => 'Meta Webhook',
            SystemActivityLog::EVENT_QUEUE_JOB       => 'Queue Actions',
        ];

        $accounts = Account::select('id', 'name', 'account_type')->orderBy('name')->get();
        $users = \App\Models\User::select('id', 'name', 'email', 'role')->orderBy('name')->get();

        return view('admin.system_activity.index', compact(
            'stats',
            'logs',
            'failedJobs',
            'activeTab',
            'eventTypes',
            'accounts',
            'users'
        ));
    }

    /**
     * Retry a single failed job by ID
     */
    public function retryJob(string|int $id, QueueMonitorService $queueService): RedirectResponse
    {
        $result = $queueService->retryJob($id);

        return redirect()
            ->route('admin.system-activity.index', ['tab' => 'queue'])
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Retry all failed jobs
     */
    public function retryAll(QueueMonitorService $queueService): RedirectResponse
    {
        $result = $queueService->retryAll();

        return redirect()
            ->route('admin.system-activity.index', ['tab' => 'queue'])
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Purge / Delete a single failed job
     */
    public function deleteFailedJob(string|int $id, QueueMonitorService $queueService): RedirectResponse
    {
        $result = $queueService->deleteFailedJob($id);

        return redirect()
            ->route('admin.system-activity.index', ['tab' => 'queue'])
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Clear all failed jobs
     */
    public function clearAllFailed(QueueMonitorService $queueService): RedirectResponse
    {
        $result = $queueService->clearAllFailed();

        return redirect()
            ->route('admin.system-activity.index', ['tab' => 'queue'])
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Prune activity logs
     */
    public function clearLogs(Request $request): RedirectResponse
    {
        $days = (int) $request->input('days', 30);

        if ($days === 0) {
            SystemActivityLog::truncate();
            $msg = 'All system activity logs cleared.';
        } else {
            $deleted = ActivityLogger::prune($days);
            $msg = "Cleaned up {$deleted} activity log records older than {$days} days.";
        }

        return redirect()
            ->route('admin.system-activity.index', ['tab' => 'logs'])
            ->with('success', $msg);
    }
}
