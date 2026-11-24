<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\ScheduledPost;
use App\Models\User;
use App\Services\PostSafetyGuard;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostSafetyGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_detects_blocked_url_shorteners_and_allows_legitimate_domains(): void
    {
        $this->assertEquals('bit.ly', PostSafetyGuard::checkBlockedDomains('Check this link: http://bit.ly/sample123'));
        $this->assertEquals('tinyurl.com', PostSafetyGuard::checkBlockedDomains('Visit https://tinyurl.com/event'));
        $this->assertEquals('mega.nz', PostSafetyGuard::checkBlockedDomains('Download files from https://mega.nz/file/abc'));
        $this->assertEquals('cutt.ly', PostSafetyGuard::checkBlockedDomains('Info: cutt.ly/xyz'));

        // Normal legitimate domains should NOT be blocked
        $this->assertNull(PostSafetyGuard::checkBlockedDomains('Visit our official portal at https://delhigovt.nic.in'));
        $this->assertNull(PostSafetyGuard::checkBlockedDomains('Official site: https://mycampaign.com/updates'));
        $this->assertNull(PostSafetyGuard::checkBlockedDomains('No links here, just plain text!'));
    }

    public function test_counts_hashtags_accurately_including_unicode(): void
    {
        $text = 'Namaste India #development #Bharat #2026 #जय_श्री_राम #youth';
        $this->assertEquals(5, PostSafetyGuard::countHashtags($text));

        $this->assertEquals(0, PostSafetyGuard::countHashtags('Plain text without tags'));
    }

    public function test_enforces_daily_limit_of_20_posts_per_account(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'City Channel',
            'account_type' => Account::TYPE_FACEBOOK,
            'access_token' => 'mock_token',
        ]);

        $targetDate = Carbon::today()->setHour(14);

        // Schedule 19 posts for today
        for ($i = 0; $i < 19; $i++) {
            ScheduledPost::create([
                'user_id' => $user->id,
                'account_id' => $account->id,
                'scheduled_at' => $targetDate->copy()->addMinutes($i * 15),
                'status' => ScheduledPost::STATUS_SCHEDULED,
            ]);
        }

        // 19 posts: still below limit
        $this->assertNull(PostSafetyGuard::checkDailyLimit($account->id, $targetDate));

        // Create 20th post
        ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'scheduled_at' => $targetDate->copy()->addMinutes(300),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        // 20 posts: limit reached!
        $this->assertEquals(20, PostSafetyGuard::checkDailyLimit($account->id, $targetDate));
    }

    public function test_detects_collision_when_another_post_is_scheduled_within_10_minutes(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'Ward Channel',
            'account_type' => Account::TYPE_FACEBOOK,
            'access_token' => 'mock_token',
        ]);

        $baseTime = Carbon::now()->addHours(3);

        $existing = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'scheduled_at' => $baseTime,
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        // Attempting to schedule 5 minutes later -> collision!
        $collidingTime = $baseTime->copy()->addMinutes(5);
        $found = PostSafetyGuard::checkCollision($account->id, $collidingTime);
        $this->assertNotNull($found);
        $this->assertEquals($existing->id, $found->id);

        // Attempting to schedule 15 minutes later -> safe, no collision!
        $safeTime = $baseTime->copy()->addMinutes(15);
        $this->assertNull(PostSafetyGuard::checkCollision($account->id, $safeTime));
    }
}
