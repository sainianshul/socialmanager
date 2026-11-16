<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use App\Models\ScheduledPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiUserScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_only_shows_data_of_logged_in_user(): void
    {
        // User A
        $userA = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'User Alice']);
        $accountA = Account::create([
            'user_id' => $userA->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '11111111',
            'name' => 'Alice Page',
            'followers_count' => 500,
            'access_token' => 'token_a',
            'is_active' => true,
        ]);
        $postA = Post::create([
            'account_id' => $accountA->id,
            'meta_post_id' => 'post_a_1',
            'message' => 'Alice Post Content',
            'published_at' => now(),
        ]);
        $scheduledA = ScheduledPost::create([
            'user_id' => $userA->id,
            'account_id' => $accountA->id,
            'post_type' => ScheduledPost::TYPE_TEXT,
            'content' => 'Alice Scheduled Content',
            'scheduled_at' => now()->addDays(2),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        // User B
        $userB = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'User Bob']);
        $accountB = Account::create([
            'user_id' => $userB->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '22222222',
            'name' => 'Bob Secret Page',
            'followers_count' => 9999,
            'access_token' => 'token_b',
            'is_active' => true,
        ]);
        $postB = Post::create([
            'account_id' => $accountB->id,
            'meta_post_id' => 'post_b_1',
            'message' => 'Bob Secret Post',
            'published_at' => now(),
        ]);

        // Alice logs in and views dashboard
        $response = $this->actingAs($userA)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Alice should see her own account and post
        $response->assertSee('Alice Page');
        $response->assertSee('Alice Post Content');

        // Alice should NOT see Bob's account or Bob's post
        $response->assertDontSee('Bob Secret Page');
        $response->assertDontSee('Bob Secret Post');
    }

    public function test_user_cannot_see_or_modify_another_users_accounts(): void
    {
        $userA = User::factory()->create(['role' => User::ROLE_USER]);
        $userB = User::factory()->create(['role' => User::ROLE_USER]);

        $accountB = Account::create([
            'user_id' => $userB->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '22222222',
            'name' => 'Bob Secret Channel',
            'access_token' => 'token_b',
            'is_active' => true,
        ]);

        // User A visits accounts index
        $response = $this->actingAs($userA)->get(route('admin.accounts.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Bob Secret Channel');

        // User A attempts to update User B's account settings
        $forbiddenResponse = $this->actingAs($userA)->post(route('admin.accounts.update-settings', $accountB->id), [
            'ai_tone' => 'Formal',
            'ai_language' => 'English',
        ]);

        $forbiddenResponse->assertStatus(403);
    }

    public function test_user_cannot_access_or_manage_another_users_scheduled_posts(): void
    {
        $userA = User::factory()->create(['role' => User::ROLE_USER]);
        $userB = User::factory()->create(['role' => User::ROLE_USER]);

        $accountB = Account::create([
            'user_id' => $userB->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '22222222',
            'name' => 'Bob Channel',
            'access_token' => 'token_b',
            'is_active' => true,
        ]);

        $scheduledB = ScheduledPost::create([
            'user_id' => $userB->id,
            'account_id' => $accountB->id,
            'post_type' => ScheduledPost::TYPE_TEXT,
            'content' => 'Bob Confidential Draft',
            'scheduled_at' => now()->addDays(3),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        // User A visits schedule page
        $response = $this->actingAs($userA)->get(route('admin.posts.schedule'));
        $response->assertStatus(200);
        $response->assertDontSee('Bob Confidential Draft');

        // User A attempts to view details of User B's scheduled post
        $showResponse = $this->actingAs($userA)->getJson(route('admin.posts.schedule.show', $scheduledB->id));
        $showResponse->assertStatus(403);

        // User A attempts to delete User B's scheduled post
        $deleteResponse = $this->actingAs($userA)->delete(route('admin.posts.schedule.destroy', $scheduledB->id));
        $deleteResponse->assertStatus(403);
    }

    public function test_user_cannot_access_another_users_comments(): void
    {
        $userA = User::factory()->create(['role' => User::ROLE_USER]);
        $userB = User::factory()->create(['role' => User::ROLE_USER]);

        $accountB = Account::create([
            'user_id' => $userB->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '22222222',
            'name' => 'Bob Channel',
            'access_token' => 'token_b',
            'is_active' => true,
        ]);

        $postB = Post::create([
            'account_id' => $accountB->id,
            'meta_post_id' => 'post_b_99',
            'message' => 'Bob Post',
        ]);

        $commentB = Comment::create([
            'post_id' => $postB->id,
            'account_id' => $accountB->id,
            'meta_comment_id' => 'comment_b_99',
            'commenter_name' => 'Secret Customer',
            'message' => 'Private feedback message',
            'status' => Comment::STATUS_NEW,
        ]);

        // User A attempts to approve or toggle hide User B's comment
        $approveResponse = $this->actingAs($userA)->post(route('admin.comments.approve', $commentB->id));
        $approveResponse->assertStatus(403);

        $hideResponse = $this->actingAs($userA)->post(route('admin.comments.toggle-hide', $commentB->id));
        $hideResponse->assertStatus(403);
    }

    public function test_regular_user_cannot_access_user_management(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.users.index'));
        $response->assertStatus(403);

        $createResponse = $this->actingAs($user)->get(route('admin.users.create'));
        $createResponse->assertStatus(403);
    }
}
