<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class YouTubeAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_youtube_redirect_requires_auth(): void
    {
        $response = $this->get(route('youtube.redirect'));
        $response->assertRedirect(route('login'));
    }

    public function test_youtube_redirect_generates_valid_google_oauth_url(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('youtube.redirect'));

        $response->assertStatus(302);
        $targetUrl = $response->getTargetUrl();

        $this->assertStringContainsString('accounts.google.com/o/oauth2/v2/auth', $targetUrl);
        $this->assertStringContainsString('access_type=offline', $targetUrl);
        $this->assertStringContainsString('prompt=consent', $targetUrl);
        $this->assertStringContainsString('youtube.readonly', $targetUrl);
        $this->assertStringContainsString('youtube.upload', $targetUrl);
        $this->assertStringContainsString('youtube.force-ssl', $targetUrl);
    }

    public function test_youtube_callback_handles_user_cancellation(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('youtube.callback', [
            'error' => 'access_denied',
            'error_description' => 'The user denied request to access their account.',
        ]));

        $response->assertRedirect(route('admin.accounts.index'));
        $response->assertSessionHas('error');
    }

    public function test_youtube_callback_successful_exchange_and_account_creation(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        Http::fake([
            // 1. Google OAuth Token exchange
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token'  => 'mock_google_access_token',
                'refresh_token' => 'mock_google_refresh_token',
                'expires_in'    => 3600,
                'token_type'    => 'Bearer',
            ], 200),

            // 2. YouTube Data API v3 channels endpoint
            'https://www.googleapis.com/youtube/v3/channels*' => Http::response([
                'items' => [
                    [
                        'id' => 'UC_TestChannel123456',
                        'snippet' => [
                            'title' => 'Code with Tech Lead',
                            'customUrl' => '@codewithtech',
                            'thumbnails' => [
                                'medium' => [
                                    'url' => 'https://yt3.ggpht.com/channel_avatar.jpg',
                                ],
                            ],
                        ],
                        'statistics' => [
                            'subscriberCount' => '45200',
                            'videoCount' => '128',
                            'viewCount' => '1250000',
                            'commentCount' => '8400',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->get(route('youtube.callback', [
            'code' => 'mock_google_auth_code_987',
        ]));

        $response->assertRedirect(route('admin.accounts.index'));
        $response->assertSessionHas('success');

        // Verify account persisted in database
        $account = Account::where('user_id', $user->id)
            ->where('account_type', Account::TYPE_YOUTUBE)
            ->where('youtube_channel_id', 'UC_TestChannel123456')
            ->first();

        $this->assertNotNull($account);
        $this->assertEquals('Code with Tech Lead', $account->name);
        $this->assertEquals('codewithtech', $account->username);
        $this->assertEquals(45200, $account->subscriber_count);
        $this->assertEquals(128, $account->video_count);
        $this->assertEquals('mock_google_access_token', $account->access_token);
        $this->assertEquals('mock_google_refresh_token', $account->google_refresh_token);
        $this->assertTrue($account->is_active);
    }
}

