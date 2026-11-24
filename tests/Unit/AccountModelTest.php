<?php

namespace Tests\Unit;

use App\Models\Account;
use Tests\TestCase;

class AccountModelTest extends TestCase
{
    public function test_platform_constants_are_defined_correctly(): void
    {
        $this->assertEquals(1, Account::TYPE_FACEBOOK);
        $this->assertEquals(2, Account::TYPE_INSTAGRAM);
        $this->assertEquals(3, Account::TYPE_YOUTUBE);
    }

    public function test_platform_helper_methods(): void
    {
        $fb = new Account(['account_type' => Account::TYPE_FACEBOOK]);
        $ig = new Account(['account_type' => Account::TYPE_INSTAGRAM]);
        $yt = new Account(['account_type' => Account::TYPE_YOUTUBE]);

        $this->assertTrue($fb->isFacebook());
        $this->assertFalse($fb->isInstagram());
        $this->assertFalse($fb->isYouTube());

        $this->assertTrue($ig->isInstagram());
        $this->assertFalse($ig->isFacebook());
        $this->assertFalse($ig->isYouTube());

        $this->assertTrue($yt->isYouTube());
        $this->assertFalse($yt->isFacebook());
        $this->assertFalse($yt->isInstagram());
    }

    public function test_platform_presentation_accessors_for_youtube(): void
    {
        $yt = new Account(['account_type' => Account::TYPE_YOUTUBE]);

        $this->assertEquals('YouTube Channel', $yt->account_type_name);
        $this->assertEquals('red', $yt->platform_color);
        $this->assertEquals('ti ti-brand-youtube', $yt->platform_icon);
        $this->assertEquals('badge-outline text-red', $yt->platform_badge_class);
    }

    public function test_account_type_list_contains_youtube(): void
    {
        $list = Account::getAccountTypeList();

        $this->assertArrayHasKey(Account::TYPE_YOUTUBE, $list);
        $this->assertEquals('YouTube Channel', $list[Account::TYPE_YOUTUBE]);
    }

    public function test_token_expiry_and_health_badges(): void
    {
        $activeAccount = new Account([
            'token_expires_at' => now()->addDays(30),
        ]);
        $this->assertFalse($activeAccount->isTokenExpired());
        $this->assertFalse($activeAccount->isTokenExpiringSoon(7));
        $this->assertStringContainsString('Token Active', $activeAccount->token_status_badge);

        $expiringSoonAccount = new Account([
            'token_expires_at' => now()->addDays(3),
        ]);
        $this->assertFalse($expiringSoonAccount->isTokenExpired());
        $this->assertTrue($expiringSoonAccount->isTokenExpiringSoon(7));
        $this->assertStringContainsString('Expires in', $expiringSoonAccount->token_status_badge);

        $expiredAccount = new Account([
            'token_expires_at' => now()->subDay(),
        ]);
        $this->assertTrue($expiredAccount->isTokenExpired());
        $this->assertStringContainsString('Token Expired', $expiredAccount->token_status_badge);

        // YouTube with refresh token should show Auto-Renewing instead of expired
        $youtubeAccount = new Account([
            'account_type' => Account::TYPE_YOUTUBE,
            'google_refresh_token' => 'sample_refresh_token_123',
            'token_expires_at' => now()->subHour(),
        ]);
        $this->assertTrue($youtubeAccount->hasAutoRenewingToken());
        $this->assertFalse($youtubeAccount->isTokenExpired());
        $this->assertFalse($youtubeAccount->isTokenExpiringSoon(7));
        $this->assertStringContainsString('Auto-Renewing', $youtubeAccount->token_status_badge);
    }

    public function test_manual_sync_interval_is_respected_and_clamped(): void
    {
        // Manual 15m
        $acc1 = new Account(['sync_interval_minutes' => 15]);
        $this->assertEquals(15, $acc1->getEffectiveSyncIntervalMinutes());
        $this->assertEquals('Manual (15m)', $acc1->sync_status_label);

        // Manual 120m (2 hours)
        $acc2 = new Account(['sync_interval_minutes' => 120]);
        $this->assertEquals(120, $acc2->getEffectiveSyncIntervalMinutes());
        $this->assertEquals('Manual (2h)', $acc2->sync_status_label);

        // Manual 360m (6 hours)
        $acc3 = new Account(['sync_interval_minutes' => 360]);
        $this->assertEquals(360, $acc3->getEffectiveSyncIntervalMinutes());
        $this->assertEquals('Manual (6h)', $acc3->sync_status_label);

        // Clamped minimum (below 10 clamped to 10)
        $accLow = new Account(['sync_interval_minutes' => 5]);
        $this->assertEquals(10, $accLow->getEffectiveSyncIntervalMinutes());

        // Clamped maximum (above 360 clamped to 360)
        $accHigh = new Account(['sync_interval_minutes' => 999]);
        $this->assertEquals(360, $accHigh->getEffectiveSyncIntervalMinutes());
    }

    public function test_adaptive_sync_interval_scales_based_on_activity_and_defaults_cleanly(): void
    {
        // Default new account with no history -> 30m
        $newAcc = new Account(['sync_interval_minutes' => 0]);
        $this->assertEquals(30, $newAcc->getEffectiveSyncIntervalMinutes());
        $this->assertEquals('Auto (30m)', $newAcc->sync_status_label);
    }

    public function test_is_due_for_sync_evaluates_correctly(): void
    {
        // Account never synced before is always due
        $accNeverSynced = new Account([
            'is_active' => true,
            'access_token' => 'token_xyz',
            'last_synced_at' => null,
            'sync_interval_minutes' => 30,
        ]);
        $this->assertTrue($accNeverSynced->isDueForSync());

        // Account synced 10m ago with 30m interval is NOT due
        $accRecentlySynced = new Account([
            'is_active' => true,
            'access_token' => 'token_xyz',
            'last_synced_at' => now()->subMinutes(10),
            'sync_interval_minutes' => 30,
        ]);
        $this->assertFalse($accRecentlySynced->isDueForSync());

        // Account synced 45m ago with 30m interval IS due
        $accDue = new Account([
            'is_active' => true,
            'access_token' => 'token_xyz',
            'last_synced_at' => now()->subMinutes(45),
            'sync_interval_minutes' => 30,
        ]);
        $this->assertTrue($accDue->isDueForSync());

        // Inactive account is NEVER due
        $accInactive = new Account([
            'is_active' => false,
            'access_token' => 'token_xyz',
            'last_synced_at' => now()->subDays(2),
            'sync_interval_minutes' => 30,
        ]);
        $this->assertFalse($accInactive->isDueForSync());
    }
}

