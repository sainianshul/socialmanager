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
                            {--queue-ai : Queue AI analysis job for each fetched comment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize latest comments from connected YouTube channels via YouTube Data API v3 and queue AI analysis';

    /**
     * Execute the console command.
     */
    public function handle(YouTubeCommentSyncService $syncService): int
    {
        $accountId = $this->option('account_id');
        $limit = (int) ($this->option('limit') ?: 50);
        $queueAi = (bool) $this->option('queue-ai');

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

        $this->info("Found {$channels->count()} active YouTube channel(s) to synchronize comments.");

        $totalSynced = 0;
        $failedCount = 0;

        foreach ($channels as $channel) {
            $this->line("Fetching comments for channel: {$channel->name} (Channel ID: {$channel->youtube_channel_id})...");

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
        $this->info("Comment sync completed! Total comments synced: {$totalSynced}, Channels failed: {$failedCount}");

        return Command::SUCCESS;
    }
}

