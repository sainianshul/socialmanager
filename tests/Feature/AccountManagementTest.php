<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_shows_accounts_and_hides_users_for_regular_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.accounts.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Regular user should see Accounts and Content
        $this->assertStringContainsString('Connected Accounts</span>', $content);
        $this->assertStringContainsString('New Post</span>', $content);

        // Regular user should NOT see Users or System settings/API in sidebar
        $this->assertStringNotContainsString('All Users</span>', $content);
        $this->assertStringNotContainsString('Blocked Users</span>', $content);
        $this->assertStringNotContainsString('API Docs (Swagger)</span>', $content);
    }

    public function test_sidebar_shows_users_and_system_and_hides_accounts_for_admin(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Admin sees Users and System & API in sidebar
        $this->assertStringContainsString('All Users</span>', $content);
        $this->assertStringContainsString('Add User</span>', $content);
        $this->assertStringContainsString('API Docs (Swagger)</span>', $content);

        // Admin does NOT see personal Connected Accounts or Content creation (managed via Ghost/Shadow mode)
        $this->assertStringNotContainsString('Connected Accounts</span>', $content);
        $this->assertStringNotContainsString('New Post</span>', $content);
    }

    public function test_accounts_empty_state_renders_when_no_accounts_exist(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.accounts.index'));

        $response->assertStatus(200);
        $response->assertSee('No Accounts Connected');
        $response->assertSee(route('facebook.redirect'));
        $response->assertSee('YouTube Channel');
        $response->assertSee('Facebook Page');
        $response->assertSee('Instagram Business');
    }

    public function test_accounts_index_renders_connected_accounts(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '1404772002715952',
            'name' => 'Healthiiiii Official',
            'category' => 'Public Figure',
            'followers_count' => 15000,
            'access_token' => 'dummy_token_123',
            'is_active' => true,
            'webhook_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('admin.accounts.index'));

        $response->assertStatus(200);
        $response->assertSee('Healthiiiii Official');
        $response->assertSee('15,000');
        $response->assertSee('Facebook Page');
    }

    public function test_update_account_ai_settings(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '1404772002715952',
            'name' => 'Healthiiiii Official',
            'access_token' => 'dummy_token_123',
            'ai_tone' => 'Humble & Respectful',
            'ai_language' => 'Hindi + Hinglish',
            'auto_reply_mode' => true,
            'auto_hide_trolls' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.accounts.update-settings', $account->id), [
            'ai_tone' => 'Patriotic & Inspiring',
            'ai_language' => 'Pure Hindi',
            'auto_reply_mode' => '1',
            'auto_hide_trolls' => '1',
            'comment_to_dm' => '1',
        ]);

        $response->assertSessionHas('success');

        $account->refresh();
        $this->assertEquals('Patriotic & Inspiring', $account->ai_tone);
        $this->assertEquals('Pure Hindi', $account->ai_language);
        $this->assertEquals(Account::AUTO_REPLY_WITH_APPROVAL, $account->auto_reply_mode);
        $this->assertTrue($account->auto_hide_trolls);
        $this->assertTrue($account->comment_to_dm);
    }

    public function test_accounts_platform_filter_and_youtube_rendering(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_1001',
            'name' => 'My FB Page',
            'followers_count' => 5000,
            'access_token' => 'dummy_fb_token',
        ]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'yt_channel_123',
            'name' => 'My YouTube Channel',
            'subscriber_count' => 12500,
            'access_token' => 'dummy_yt_token',
        ]);

        // 1. All channels view
        $allResponse = $this->actingAs($user)->get(route('admin.accounts.index'));
        $allResponse->assertStatus(200);
        $allResponse->assertSee('My FB Page');
        $allResponse->assertSee('My YouTube Channel');
        $allResponse->assertSee('YouTube Channel');
        $allResponse->assertSee('12,500');
        $allResponse->assertSee('Subscribers');

        // 2. Filter by YouTube only
        $ytResponse = $this->actingAs($user)->get(route('admin.accounts.index', ['platform' => Account::TYPE_YOUTUBE]));
        $ytResponse->assertStatus(200);
        $ytResponse->assertSee('My YouTube Channel');
        $ytResponse->assertDontSee('My FB Page');

        // 3. Filter by Facebook only
        $fbResponse = $this->actingAs($user)->get(route('admin.accounts.index', ['platform' => Account::TYPE_FACEBOOK]));
        $fbResponse->assertStatus(200);
        $fbResponse->assertSee('My FB Page');
        $fbResponse->assertDontSee('My YouTube Channel');
    }

    public function test_accounts_index_displays_token_expiry_health_status(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $healthyAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_healthy_yt',
            'name' => 'Active Channel YT',
            'token_expires_at' => now()->addDays(20),
            'access_token' => 'mock_token',
        ]);

        $expiredAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_expired',
            'name' => 'Expired FB Page',
            'token_expires_at' => now()->subDay(),
            'access_token' => 'mock_token',
        ]);

        $response = $this->actingAs($user)->get(route('admin.accounts.index'));

        $response->assertStatus(200);
        $response->assertSee('Active Channel YT');
        $response->assertSee('Expired FB Page');
        $response->assertSee('Token Expired');
    }
}

