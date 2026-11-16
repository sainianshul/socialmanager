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
    }
}

