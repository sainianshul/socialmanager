<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_requires_authentication(): void
    {
        $response = $this->get(route('admin.analytics.index'));
        $response->assertRedirect('/admin/login');
    }

    public function test_analytics_dashboard_renders_successfully(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '12345678',
            'name' => 'Politician Official Page',
            'followers_count' => 15000,
            'access_token' => 'mock_token',
            'is_active' => true,
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'fb_post_101',
            'message' => 'Development project inaugurate kiya gaya',
            'likes_count' => 120,
            'comments_count' => 15,
            'shares_count' => 10,
            'published_at' => now()->subDays(2),
        ]);

        Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'c_1',
            'commenter_name' => 'Rajesh Sharma',
            'message' => 'Bahut badhiya kaam kiya aapne mantri ji!',
            'sentiment' => 'positive',
            'is_favor' => true,
            'is_against' => false,
            'is_complaint' => false,
            'category' => 'appreciation',
            'status' => Comment::STATUS_REPLIED,
            'commented_at' => now()->subDay(),
        ]);

        Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'c_2',
            'commenter_name' => 'Sunil Verma',
            'message' => 'Sadak abhi tak kharab padi hai please theek karwayein',
            'sentiment' => 'negative',
            'is_favor' => false,
            'is_against' => true,
            'is_complaint' => true,
            'category' => 'complaint',
            'status' => Comment::STATUS_NEW,
            'commented_at' => now()->subHours(12),
        ]);

        $response = $this->actingAs($user)->get(route('admin.analytics.index'));

        $response->assertOk();
        $response->assertViewIs('admin.analytics.index');
        $response->assertSee('Politics Analyses');
        $response->assertSee('FAVOURABILITY');
        $response->assertSee('GRIEVANCES');
        $response->assertSee('TOTAL COMMENTS');
        $response->assertSee('TOTAL AUDIENCE');
        $response->assertSee('Rajesh Sharma');
        $response->assertSee('Development project inaugurate');
    }

    public function test_analytics_filters_by_platform_and_range(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_111',
            'name' => 'FB Page',
            'followers_count' => 5000,
            'access_token' => 'token_fb',
            'is_active' => true,
        ]);

        $igAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_222',
            'name' => 'IG Profile',
            'followers_count' => 12000,
            'access_token' => 'token_ig',
            'is_active' => true,
        ]);

        // Platform filter
        $responseFb = $this->actingAs($user)->get(route('admin.analytics.index', ['platform' => 'facebook', 'range' => '7d']));
        $responseFb->assertOk();
        $responseFb->assertViewHas('selectedPlatform', 'facebook');
        $responseFb->assertViewHas('timeRange', '7d');

        $responseIg = $this->actingAs($user)->get(route('admin.analytics.index', ['platform' => 'instagram', 'range' => '90d']));
        $responseIg->assertOk();
        $responseIg->assertViewHas('selectedPlatform', 'instagram');
        $responseIg->assertViewHas('timeRange', '90d');
    }

    public function test_analytics_scopes_data_to_authenticated_user(): void
    {
        $userA = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'Leader A']);
        $accountA = Account::create([
            'user_id' => $userA->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'acc_a',
            'name' => 'Leader A Page',
            'followers_count' => 1000,
            'access_token' => 'token_a',
            'is_active' => true,
        ]);
        $postA = Post::create([
            'account_id' => $accountA->id,
            'meta_post_id' => 'post_a',
            'message' => 'Leader A Special Speech',
            'published_at' => now(),
        ]);

        $userB = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'Leader B']);
        $accountB = Account::create([
            'user_id' => $userB->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'acc_b',
            'name' => 'Leader B Page',
            'followers_count' => 2000,
            'access_token' => 'token_b',
            'is_active' => true,
        ]);
        $postB = Post::create([
            'account_id' => $accountB->id,
            'meta_post_id' => 'post_b',
            'message' => 'Leader B Secret Meeting',
            'published_at' => now(),
        ]);

        // Leader A visits analytics -> sees post A, does NOT see post B
        $responseA = $this->actingAs($userA)->get(route('admin.analytics.index'));
        $responseA->assertOk();
        $responseA->assertSee('Leader A Special Speech');
        $responseA->assertDontSee('Leader B Secret Meeting');
    }
}
