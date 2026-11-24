<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\YouTubeCommentSyncService;
use Illuminate\Console\Command;

class SyncYouTubeCommentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:youtube-comments 
                            {--account_id= : Sync comments for a specific YouTube channel ID} 
                            {--limit=50 : Number of comments to fetch per channel}
                            {--queue-ai : Queue AI analysis job for each fetched comment}
                            {--force : Force sync regardless of adaptive interval window}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize latest comments from connected YouTube channels via YouTube Data API v3 using Smart Adaptive Polling';

    /**
     * Execute the console command.
     */
    public function handle(YouTubeCommentSyncService $syncService): int
    {
        $accountId = $this->option('account_id');
        $limit = (int) ($this->option('limit') ?: 50);
        $queueAi = (bool) $this->option('queue-ai');
        $force = (bool) $this->option('force');

        $query = Account::where('account_type', Account::TYPE_YOUTUBE)
            ->where('is_active', true);

        if ($accountId) {
            $query->where('id', $accountId);
        }

        $channels = $query->get();

        if ($channels->isEmpty()) {
            $this->warn('No active YouTube channels found to synchronize comments.');
            return Command::SUCCESS;
        }

        $this->info("Found {$channels->count()} active YouTube channel(s) to inspect for comment sync.");

        $totalSynced = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($channels as $channel) {
            // Smart Adaptive check: skip if not due yet
            if (!$force && !$channel->isDueForSync()) {
                $lastSynced = $channel->last_synced_at ? $channel->last_synced_at->diffForHumans() : 'Never';
                $this->line("⊘ Skipped [YouTube] {$channel->name} (Adaptive interval: {$channel->sync_status_label}, last synced: {$lastSynced})");
                $skippedCount++;
                continue;
            }

            $this->line("Fetching comments for channel: {$channel->name} (Channel ID: {$channel->youtube_channel_id}, Interval: {$channel->sync_status_label})...");

            $result = $syncService->syncAccountComments($channel, 10, $limit, $queueAi);

            if ($result['success']) {
                $count = $result['synced_count'];
                $totalSynced += $count;
                $this->info("✓ Successfully synced {$count} comment(s) for {$channel->name}.");
            } else {
                $failedCount++;
                $this->error("✗ Failed to sync comments for {$channel->name}: {$result['message']}");
            }
        }

        $this->newLine();
        $this->info("Comment sync completed! Total comments synced: {$totalSynced}, Skipped (not due): {$skippedCount}, Channels failed: {$failedCount}");

        return Command::SUCCESS;
    }
}

