<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountTokenEncryptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_tokens_are_transparently_encrypted_in_database(): void
    {
        $user = User::factory()->create();

        $rawToken = 'EAABsb_facebook_secret_long_token_1234567890';
        $rawGoogleToken = '1//04_google_secret_refresh_token_xyz987';

        $account = Account::create([
            'user_id'              => $user->id,
            'account_type'         => Account::TYPE_FACEBOOK,
            'name'                 => 'Encrypted Page',
            'meta_account_id'      => '123456789',
            'access_token'         => $rawToken,
            'google_refresh_token' => $rawGoogleToken,
            'is_active'            => true,
        ]);

        // 1. Through model getter, tokens are automatically decrypted and readable
        $this->assertEquals($rawToken, $account->access_token);
        $this->assertEquals($rawGoogleToken, $account->google_refresh_token);

        // 2. In raw database row, tokens are physically encrypted strings, NOT plaintext
        $rawDbRow = DB::table('accounts')->where('id', $account->id)->first();
        $this->assertNotEquals($rawToken, $rawDbRow->access_token);
        $this->assertNotEquals($rawGoogleToken, $rawDbRow->google_refresh_token);

        // Decrypting the raw DB string manually matches the original
        $this->assertEquals($rawToken, Crypt::decryptString($rawDbRow->access_token));
        $this->assertEquals($rawGoogleToken, Crypt::decryptString($rawDbRow->google_refresh_token));
    }

    public function test_tokens_are_hidden_from_json_and_array_serialization(): void
    {
        $user = User::factory()->create();

        $account = Account::create([
            'user_id'              => $user->id,
            'account_type'         => Account::TYPE_YOUTUBE,
            'name'                 => 'Confidential Channel',
            'youtube_channel_id'   => 'UC_secret',
            'access_token'         => 'secret_access_token',
            'google_refresh_token' => 'secret_refresh_token',
            'is_active'            => true,
        ]);

        $arrayData = $account->toArray();
        $this->assertArrayNotHasKey('access_token', $arrayData);
        $this->assertArrayNotHasKey('refresh_token', $arrayData);
        $this->assertArrayNotHasKey('google_refresh_token', $arrayData);

        $jsonData = json_encode($account);
        $this->assertStringNotContainsString('secret_access_token', $jsonData);
        $this->assertStringNotContainsString('secret_refresh_token', $jsonData);
    }

    public function test_legacy_plaintext_tokens_gracefully_read_without_crashing(): void
    {
        $user = User::factory()->create();

        // Directly insert raw plaintext tokens into DB (simulating legacy data)
        $id = DB::table('accounts')->insertGetId([
            'user_id'          => $user->id,
            'account_type'     => Account::TYPE_FACEBOOK,
            'name'             => 'Legacy Account',
            'meta_account_id'  => '999999',
            'access_token'     => 'legacy_unencrypted_plaintext_token',
            'is_active'        => 1,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $account = Account::find($id);
        $this->assertEquals('legacy_unencrypted_plaintext_token', $account->access_token);
    }

    public function test_artisan_encrypt_command_encrypts_legacy_tokens(): void
    {
        $user = User::factory()->create();

        $id = DB::table('accounts')->insertGetId([
            'user_id'              => $user->id,
            'account_type'         => Account::TYPE_YOUTUBE,
            'name'                 => 'Legacy Unencrypted Account',
            'youtube_channel_id'   => 'UC_legacy',
            'access_token'         => 'plaintext_access_123',
            'google_refresh_token' => 'plaintext_refresh_456',
            'is_active'            => 1,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        $this->artisan('accounts:encrypt-tokens')
            ->expectsOutputToContain('Scanning 1 accounts for unencrypted tokens...')
            ->assertSuccessful();

        $rawRow = DB::table('accounts')->where('id', $id)->first();
        $this->assertNotEquals('plaintext_access_123', $rawRow->access_token);
        $this->assertEquals('plaintext_access_123', Crypt::decryptString($rawRow->access_token));
    }
}
