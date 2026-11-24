<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ScheduledPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostSafetyGuardFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Account $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->account = Account::create([
            'user_id'           => $this->admin->id,
            'account_type'      => Account::TYPE_FACEBOOK,
            'account_id'        => 'fb_page_123',
            'name'              => 'Test Facebook Page',
            'access_token'      => 'valid_token_abc',
            'is_active'         => true,
            'followers_count'   => 100,
        ]);
    }

    public function test_check_guard_endpoint_detects_blocked_domains_and_hashtag_overflow()
    {
        $response = $this->actingAs($this->admin)->postJson(route('admin.posts.schedule.check-guard'), [
            'content'        => 'Check this link bit.ly/promo #one #two #three',
            'account_id'     => $this->account->id,
            'scheduled_at'   => now()->addHour()->toDateTimeString(),
        ]);

        $response->assertOk()
            ->assertJson([
                'valid' => false,
                'hashtag_count' => 3,
                'max_hashtags' => 20,
            ]);

        $this->assertNotEmpty($response->json('errors'));
        $this->assertStringContainsString('bit.ly', $response->json('errors')[0]);
    }

    public function test_check_guard_endpoint_detects_time_collision()
    {
        $scheduledTime = Carbon::parse('2026-12-01 10:00:00');

        // Create an existing post at 10:05:00 (5 mins away, within 10 min window)
        ScheduledPost::create([
            'user_id'        => $this->admin->id,
            'account_id'     => $this->account->id,
            'platform'       => Account::TYPE_FACEBOOK,
            'post_type'      => 'text',
            'content'        => 'Existing scheduled post',
            'scheduled_at'   => Carbon::parse('2026-12-01 10:05:00'),
            'status'         => ScheduledPost::STATUS_SCHEDULED,
            'privacy_status' => 'public',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.posts.schedule.check-guard'), [
            'account_id'   => $this->account->id,
            'scheduled_at' => $scheduledTime->toDateTimeString(),
            'content'      => 'Clean new post',
        ]);

        $response->assertOk()
            ->assertJson([
                'valid' => true,
                'has_collision' => true,
            ]);

        $this->assertNotEmpty($response->json('warnings'));
        $this->assertStringContainsString('10-minute gap', $response->json('warnings')[0]);
    }

    public function test_store_rejects_blocked_shorteners()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.posts.schedule.store'), [
            'account_ids'   => [$this->account->id],
            'publish_mode'  => 'schedule',
            'scheduled_at'  => now()->addDay()->format('Y-m-d H:i'),
            'post_type'     => 'text',
            'content'       => 'Download file from mega.nz/file123',
        ]);

        $response->assertSessionHasErrors(['content']);
        $this->assertEquals(0, ScheduledPost::count());
    }

    public function test_store_rejects_hashtag_overflow()
    {
        $tags = implode(' ', array_map(fn($i) => "#tag{$i}", range(1, 25)));

        $response = $this->actingAs($this->admin)->post(route('admin.posts.schedule.store'), [
            'account_ids'   => [$this->account->id],
            'publish_mode'  => 'schedule',
            'scheduled_at'  => now()->addDay()->format('Y-m-d H:i'),
            'post_type'     => 'text',
            'content'       => "Here is my caption with {$tags}",
        ]);

        $response->assertSessionHasErrors(['content']);
        $this->assertEquals(0, ScheduledPost::count());
    }

    public function test_store_rejects_time_collision()
    {
        $time = Carbon::parse('2026-12-05 14:00:00');

        ScheduledPost::create([
            'user_id'        => $this->admin->id,
            'account_id'     => $this->account->id,
            'platform'       => Account::TYPE_FACEBOOK,
            'post_type'      => 'text',
            'content'        => 'First post',
            'scheduled_at'   => $time,
            'status'         => ScheduledPost::STATUS_SCHEDULED,
            'privacy_status' => 'public',
        ]);

        // Attempt to schedule another post at 14:04 (4 min gap, must be rejected)
        $response = $this->actingAs($this->admin)->post(route('admin.posts.schedule.store'), [
            'account_ids'   => [$this->account->id],
            'publish_mode'  => 'schedule',
            'scheduled_at'  => Carbon::parse('2026-12-05 14:04:00')->format('Y-m-d H:i'),
            'post_type'     => 'text',
            'content'       => 'Colliding post message',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, ScheduledPost::count());
    }
}
