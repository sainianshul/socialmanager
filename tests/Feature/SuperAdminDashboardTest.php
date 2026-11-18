<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_dashboard_renders_system_overview_metrics(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Admin Boss']);
        $clientA = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'Client A']);
        $clientB = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'Client B']);

        $accountA = Account::create([
            'user_id' => $clientA->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_101',
            'name' => 'Politician A Page',
            'access_token' => 'token_a',
            'is_active' => true,
        ]);

        $postA = Post::create([
            'account_id' => $accountA->id,
            'meta_post_id' => 'p_101',
            'message' => 'Public statement 1',
            'published_at' => now(),
        ]);

        Comment::create([
            'account_id' => $accountA->id,
            'post_id' => $postA->id,
            'meta_comment_id' => 'c_101',
            'commenter_name' => 'Citizen John',
            'message' => 'Great initiative!',
            'status' => Comment::STATUS_REPLIED,
            'ai_reply' => 'Thank you Citizen John for your support!',
            'sentiment' => 'positive',
            'is_favor' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Super Admin Console');
        $response->assertSee('System Overview & Administration', false);
        $response->assertSee('TOTAL USERS');
        $response->assertSee('AI REPLIES');
        $response->assertSee('TOTAL POSTS');
        $response->assertSee('TOTAL COMMENTS');
        $response->assertSee('FACEBOOK CONNECTED');
        $response->assertSee('INSTAGRAM CONNECTED');
        $response->assertSee('YOUTUBE CONNECTED');

        // Verify removed sections are NOT rendered
        $response->assertDontSee('Recent Registered Users');
        $response->assertDontSee('Live AI & Constituent Stream', false);
        $response->assertDontSee('Platform Infrastructure');
        $response->assertDontSee('GRIEVANCES FLAGGED');
        $response->assertDontSee('TROLLS / ABUSE BLOCKED');
    }

    public function test_sidebar_hides_politics_analyses_for_super_admin(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Admin should see All Users, Add User, but NOT Politics Analyses in sidebar
        $response->assertSee('All Users</span>', false);
        $response->assertDontSee('Politics Analyses</span>', false);
    }

    public function test_sidebar_shows_politics_analyses_for_regular_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Regular user should see Politics Analyses in sidebar
        $response->assertSee('Politics Analyses</span>', false);
        // Regular user should NOT see All Users in sidebar
        $response->assertDontSee('All Users</span>', false);
    }
}
