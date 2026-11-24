<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\MetaCommentSyncService;
use Illuminate\Console\Command;

class SyncMetaCommentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:meta-comments 
                            {--account_id= : Sync comments for a specific account ID} 
                            {--post_limit=10 : Number of recent posts to inspect per account}
                            {--comment_limit=25 : Number of comments to fetch per post}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize latest comments from connected Facebook Pages and Instagram accounts via Meta Graph API';

    /**
     * Execute the console command.
     */
    public function handle(MetaCommentSyncService $syncService): int
    {
        $accountId = $this->option('account_id');
        $postLimit = (int) ($this->option('post_limit') ?: 10);
        $commentLimit = (int) ($this->option('comment_limit') ?: 25);

        $query = Account::whereIn('account_type', [Account::TYPE_FACEBOOK, Account::TYPE_INSTAGRAM])
            ->where('is_active', true);

        if ($accountId) {
            $query->where('id', $accountId);
        }

        $accounts = $query->get();

        if ($accounts->isEmpty()) {
            $this->warn('No active Facebook or Instagram accounts found to synchronize comments.');
            return Command::SUCCESS;
        }

        $this->info("Found {$accounts->count()} active Meta account(s) to synchronize comments.");

        $totalSynced = 0;
        $failedCount = 0;

        foreach ($accounts as $account) {
            $platformName = $account->isFacebook() ? 'Facebook' : 'Instagram';
            $this->line("Fetching comments for [{$platformName}] {$account->name} (Account #{$account->id})...");

            $result = $syncService->syncAccountComments($account, $postLimit, $commentLimit);

            if ($result['success']) {
                $count = $result['synced_count'];
                $totalSynced += $count;
                $this->info("✓ Successfully synced {$count} comment(s) for {$account->name}.");
            } else {
                $failedCount++;
                $this->error("✗ Notice for {$account->name}: {$result['message']}");
            }
        }

        $this->newLine();
        $this->info("Meta comment sync completed! Total comments synced: {$totalSynced}, Accounts with notices/failures: {$failedCount}");

        return Command::SUCCESS;
    }
}
