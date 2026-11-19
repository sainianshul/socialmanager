<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TailWebhookLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:logs 
                            {--lines=50 : Number of lines to display from the end of the log}
                            {--clear : Clear the webhook log file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display or clear the dedicated Meta Webhook activity logs (storage/logs/webhook.log)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $logPath = storage_path('logs/webhook.log');

        if ($this->option('clear')) {
            File::put($logPath, '');
            $this->info("Webhook log file [{$logPath}] cleared successfully.");
            return Command::SUCCESS;
        }

        if (!File::exists($logPath) || empty(trim(File::get($logPath)))) {
            $this->warn("No webhook activity recorded yet in: {$logPath}");
            $this->line("Trigger a webhook event on Meta or visit /webhook/meta to record live entries.");
            return Command::SUCCESS;
        }

        $lines = (int) $this->option('lines') ?: 50;
        $fileLines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $slice = array_slice($fileLines, -$lines);

        $this->info("=== Showing last " . count($slice) . " lines of Webhook Activity Logs ===");
        $this->line("File: {$logPath}\n");

        foreach ($slice as $line) {
            if (str_contains($line, '.ERROR') || str_contains($line, 'Failed')) {
                $this->error($line);
            } elseif (str_contains($line, '.WARNING')) {
                $this->warn($line);
            } elseif (str_contains($line, 'Handshake') || str_contains($line, 'Auto DM')) {
                $this->info($line);
            } else {
                $this->line($line);
            }
        }

        $this->line("\n=== End of Webhook Logs ===");
        return Command::SUCCESS;
    }
}
