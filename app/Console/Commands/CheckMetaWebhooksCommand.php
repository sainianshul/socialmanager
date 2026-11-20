<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckMetaWebhooksCommand extends Command
{
    protected $signature = 'meta:check-webhooks {--subscribe : Automatically subscribe all connected Facebook pages}';
    protected $description = 'Check Meta Graph API webhook subscription status for connected Facebook Pages and Instagram accounts';

    public function handle(): int
    {
        $apiVersion = 'v20.0';
        $accounts = Account::where('account_type', Account::TYPE_FACEBOOK)
            ->where('is_active', true)
            ->get();

        if ($accounts->isEmpty()) {
            $this->warn('No active Facebook pages found in database.');
            return Command::SUCCESS;
        }

        $this->info("Checking webhook subscription for {$accounts->count()} Facebook Page(s)...");

        foreach ($accounts as $acc) {
            $this->line("--------------------------------------------------");
            $this->info("Page #{$acc->id}: {$acc->name} (Page ID: {$acc->meta_account_id})");

            if (empty($acc->access_token)) {
                $this->error("✗ No access token stored for this page.");
                continue;
            }

            // 1. Query GET /{page_id}/subscribed_apps
            $getRes = Http::timeout(10)->get("https://graph.facebook.com/{$apiVersion}/{$acc->meta_account_id}/subscribed_apps", [
                'access_token' => $acc->access_token,
            ])->json();

            $this->line("Current Subscribed Apps: " . json_encode($getRes));

            $isSubscribed = false;
            $currentFields = [];

            if (isset($getRes['data']) && is_array($getRes['data'])) {
                foreach ($getRes['data'] as $app) {
                    $isSubscribed = true;
                    $currentFields = $app['subscribed_fields'] ?? [];
                    $this->info("✓ Subscribed to App: " . ($app['name'] ?? $app['id'] ?? 'Unknown'));
                    $this->line("  Active Subscribed Fields: " . implode(', ', $currentFields));
                }
            }

            if (!$isSubscribed || $this->option('subscribe')) {
                $this->warn("Subscribing page {$acc->name} to App webhooks with feed, mention, messages...");

                $postRes = Http::timeout(10)->post("https://graph.facebook.com/{$apiVersion}/{$acc->meta_account_id}/subscribed_apps", [
                    'subscribed_fields' => ['feed', 'mention', 'messages'],
                    'access_token'      => $acc->access_token,
                ])->json();

                if (($postRes['success'] ?? false) === true) {
                    $this->info("✓ Successfully subscribed page to webhooks!");
                } else {
                    $this->error("✗ Subscription failed: " . json_encode($postRes));
                }
            }
        }

        return Command::SUCCESS;
    }
}
