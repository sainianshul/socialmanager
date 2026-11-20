<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptAccountTokensCommand extends Command
{
    protected $signature = 'accounts:encrypt-tokens {--force : Force re-encryption of all tokens}';
    protected $description = 'Encrypt all plaintext OAuth access tokens and refresh tokens in the accounts table using AES-256';

    public function handle(): int
    {
        $accounts = DB::table('accounts')->get();

        if ($accounts->isEmpty()) {
            $this->info('No accounts found in database.');
            return Command::SUCCESS;
        }

        $this->info("Scanning {$accounts->count()} accounts for unencrypted tokens...");

        $encryptedCount = 0;
        $alreadyEncryptedCount = 0;

        foreach ($accounts as $acc) {
            $updates = [];

            // 1. Check and encrypt access_token
            if (!empty($acc->access_token)) {
                if ($this->isEncrypted($acc->access_token)) {
                    $alreadyEncryptedCount++;
                } else {
                    $updates['access_token'] = Crypt::encryptString($acc->access_token);
                }
            }

            // 2. Check and encrypt refresh_token
            if (!empty($acc->refresh_token)) {
                if (!$this->isEncrypted($acc->refresh_token)) {
                    $updates['refresh_token'] = Crypt::encryptString($acc->refresh_token);
                }
            }

            // 3. Check and encrypt google_refresh_token
            if (!empty($acc->google_refresh_token)) {
                if (!$this->isEncrypted($acc->google_refresh_token)) {
                    $updates['google_refresh_token'] = Crypt::encryptString($acc->google_refresh_token);
                }
            }

            if (!empty($updates)) {
                DB::table('accounts')->where('id', $acc->id)->update($updates);
                $encryptedCount++;
                $this->info("✓ Encrypted credentials for Account #{$acc->id} ({$acc->name})");
            }
        }

        $this->info("Encryption complete! Accounts newly encrypted: {$encryptedCount}, Already encrypted tokens: {$alreadyEncryptedCount}");

        return Command::SUCCESS;
    }

    private function isEncrypted(?string $value): bool
    {
        if (empty($value)) return false;
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
