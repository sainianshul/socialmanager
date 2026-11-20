<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\AccountTokenRefreshService;
use Illuminate\Console\Command;

class RefreshExpiringTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:refresh-tokens 
                            {--account_id= : Refresh a specific account by its database ID}
                            {--force : Force token refresh even if not yet expiring}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proactively refresh expiring OAuth access tokens (YouTube/Google) to keep connections permanently active';

    /**
     * Execute the console command.
     */
    public function handle(AccountTokenRefreshService $refreshService): int
    {
        $accountId = $this->option('account_id');
        $force = (bool) $this->option('force');

        if ($accountId) {
            $account = Account::find($accountId);
            if (!$account) {
                $this->error("Account #{$accountId} not found.");
                return Command::FAILURE;
            }

            $this->info("Refreshing token for account #{$account->id} ({$account->name})...");
            $result = $refreshService->refreshYouTubeToken($account, $force);

            if ($result['success']) {
                $status = $result['refreshed'] ? 'Refreshed' : 'Skipped (Still Valid)';
                $this->info("✓ [{$status}] {$result['message']}");
                return Command::SUCCESS;
            } else {
                $this->error("✗ [Failed] {$result['message']}");
                return Command::FAILURE;
            }
        }

        $this->info('Checking active accounts for expiring OAuth access tokens...');

        $results = $refreshService->refreshAllExpiringYouTubeTokens($force);

        $this->table(
            ['Total Checked', 'Refreshed', 'Skipped (Valid)', 'Failed'],
            [[$results['total'], $results['refreshed'], $results['skipped'], $results['failed']]]
        );

        if (!empty($results['details'])) {
            $rows = [];
            foreach ($results['details'] as $accId => $data) {
                $r = $data['result'];
                $status = $r['success'] ? ($r['refreshed'] ? '✓ Refreshed' : '— Valid') : '✗ Failed';
                $rows[] = [$accId, $data['name'], $status, $r['message']];
            }
            $this->table(['ID', 'Account Name', 'Status', 'Details'], $rows);
        }

        return $results['failed'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
