<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use App\Services\AccountTokenRefreshService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AccountTokenRefreshTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_service_refreshes_expired_youtube_token(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'new_fresh_google_access_token_xyz',
                'expires_in'   => 3600,
            ], 200),
        ]);

        $account = Account::create([
            'user_id'              => $this->user->id,
            'account_type'         => Account::TYPE_YOUTUBE,
            'name'                 => 'Test YouTube Channel',
            'youtube_channel_id'   => 'UC_test_123',
            'access_token'         => 'old_expired_token',
            'google_refresh_token' => 'valid_refresh_token_abc',
            'token_expires_at'     => now()->subMinutes(10),
            'is_active'            => true,
        ]);

        $service = new AccountTokenRefreshService();
        $result = $service->refreshYouTubeToken($account);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['refreshed']);
        $this->assertEquals('new_fresh_google_access_token_xyz', $result['token']);

        $account->refresh();
        $this->assertEquals('new_fresh_google_access_token_xyz', $account->access_token);
        $this->assertTrue($account->token_expires_at->isFuture());
    }

    public function test_service_skips_refresh_if_token_is_still_fresh(): void
    {
        Http::fake();

        $account = Account::create([
            'user_id'              => $this->user->id,
            'account_type'         => Account::TYPE_YOUTUBE,
            'name'                 => 'Fresh YouTube Channel',
            'youtube_channel_id'   => 'UC_test_456',
            'access_token'         => 'still_valid_token',
            'google_refresh_token' => 'valid_refresh_token_abc',
            'token_expires_at'     => now()->addMinutes(45),
            'is_active'            => true,
        ]);

        $service = new AccountTokenRefreshService();
        $result = $service->refreshYouTubeToken($account, force: false);

        $this->assertTrue($result['success']);
        $this->assertFalse($result['refreshed']);
        Http::assertNothingSent();
    }

    public function test_command_refreshes_expiring_tokens_successfully(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'refreshed_cron_token_999',
                'expires_in'   => 3600,
            ], 200),
        ]);

        $account = Account::create([
            'user_id'              => $this->user->id,
            'account_type'         => Account::TYPE_YOUTUBE,
            'name'                 => 'Expiring Channel',
            'youtube_channel_id'   => 'UC_expiring_789',
            'access_token'         => 'expiring_soon_token',
            'google_refresh_token' => 'cron_refresh_token',
            'token_expires_at'     => now()->addMinutes(10), // Expiring in < 30 mins
            'is_active'            => true,
        ]);

        $this->artisan('accounts:refresh-tokens')
            ->expectsOutputToContain('Checking active accounts for expiring OAuth access tokens...')
            ->assertSuccessful();

        $account->refresh();
        $this->assertEquals('refreshed_cron_token_999', $account->access_token);
    }
}
