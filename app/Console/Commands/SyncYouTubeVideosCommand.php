<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\YouTubeVideoSyncService;
use Illuminate\Console\Command;

class SyncYouTubeVideosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:youtube-videos 
                            {--account_id= : Sync a specific YouTube channel account ID} 
                            {--limit=25 : Number of videos to fetch per channel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and synchronize latest published videos and metrics (views/likes) via YouTube Data API v3';

    /**
     * Execute the console command.
     */
    public function handle(YouTubeVideoSyncService $syncService): int
    {
        $accountId = $this->option('account_id');
        $limit = (int) ($this->option('limit') ?: 25);

        $query = Account::where('account_type', Account::TYPE_YOUTUBE)
            ->where('is_active', true);

        if ($accountId) {
            $query->where('id', $accountId);
        }

        $channels = $query->get();

        if ($channels->isEmpty()) {
            $this->warn('No active YouTube channels found to synchronize.');
            return Command::SUCCESS;
        }

        $this->info("Found {$channels->count()} active YouTube channel(s) to synchronize.");

        $totalSynced = 0;
        $failedCount = 0;

        foreach ($channels as $channel) {
            $this->line("Fetching latest videos for channel: {$channel->name} (Channel ID: {$channel->youtube_channel_id})...");

            $result = $syncService->syncChannelVideos($channel, $limit);

            if ($result['success']) {
                $count = $result['synced_count'];
                $totalSynced += $count;
                $this->info("✓ Successfully synced {$count} video(s) for {$channel->name}.");
            } else {
                $failedCount++;
                $this->error("✗ Failed to sync {$channel->name}: {$result['message']}");
            }
        }

        $this->newLine();
        $this->info("Sync completed! Total videos synced: {$totalSynced}, Channels failed: {$failedCount}");

        return Command::SUCCESS;
    }
}

