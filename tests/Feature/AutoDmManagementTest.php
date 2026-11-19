<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AutoDmRule;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoDmManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_dm_requires_authentication(): void
    {
        $response = $this->get(route('admin.auto-dm.index'));
        $response->assertRedirect('/admin/login');
    }

    public function test_auto_dm_index_renders_empty_state_when_no_rules_exist(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.auto-dm.index'));

        $response->assertStatus(200);
        $response->assertSee('Auto DM Rules');
        $response->assertSee('No Auto DM Rules Found');
        $response->assertSee('Create Rule');
    }

    public function test_auto_dm_index_renders_rules_belonging_to_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '111222',
            'name' => 'User FB Page',
            'access_token' => 'token1',
        ]);

        $otherAccount = Account::create([
            'user_id' => $otherUser->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '333444',
            'name' => 'Other User Page',
            'access_token' => 'token2',
        ]);

        $userRule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'User Ebook Campaign',
            'keywords' => ['ebook', 'pdf'],
            'match_type' => 'contains',
            'dm_message' => 'Here is your ebook link: https://test.com/ebook',
            'is_active' => true,
        ]);

        $otherRule = AutoDmRule::create([
            'user_id' => $otherUser->id,
            'account_id' => $otherAccount->id,
            'name' => 'Secret Other Rule',
            'keywords' => ['secret'],
            'match_type' => 'contains',
            'dm_message' => 'Should not be seen',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('admin.auto-dm.index'));

        $response->assertStatus(200);
        $response->assertSee('User Ebook Campaign');
        $response->assertSee('User FB Page');
        $response->assertDontSee('Secret Other Rule');
    }

    public function test_user_can_view_create_page(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '111222',
            'name' => 'User FB Page',
            'access_token' => 'token1',
        ]);

        $response = $this->actingAs($user)->get(route('admin.auto-dm.create'));

        $response->assertStatus(200);
        $response->assertSee('Create Auto DM Rule');
        $response->assertSee('User FB Page');
    }

    public function test_user_can_view_edit_page(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '111222',
            'name' => 'User FB Page',
            'access_token' => 'token1',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'My Existing Rule',
            'keywords' => ['price', 'cost'],
            'match_type' => 'contains',
            'dm_message' => 'Price is $50',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('admin.auto-dm.edit', $rule->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Auto DM Rule');
        $response->assertSee('My Existing Rule');
    }

    public function test_user_can_create_auto_dm_rule_for_all_posts(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '998877',
            'name' => 'My Brand Page',
            'access_token' => 'test-token',
        ]);

        $payload = [
            'name' => 'Lead Gen Discount',
            'account_id' => $account->id,
            'target_type' => 'all',
            'keywords' => 'discount, coupon, promo, 20off',
            'match_type' => 'contains',
            'dm_message' => 'Thanks for asking! Use code SAVE20 for 20% off at checkout.',
            'public_reply' => 'Sent you a DM! Check your inbox 📥',
            'is_active' => '1',
        ];

        $response = $this->actingAs($user)->post(route('admin.auto-dm.store'), $payload);

        $response->assertRedirect(route('admin.auto-dm.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('auto_dm_rules', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Lead Gen Discount',
            'post_id' => null,
            'match_type' => 'contains',
            'is_active' => 1,
        ]);

        $rule = AutoDmRule::where('name', 'Lead Gen Discount')->first();
        $this->assertEquals(['discount', 'coupon', 'promo', '20off'], $rule->keywords);
    }

    public function test_user_can_create_auto_dm_rule_for_specific_post(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => '121212',
            'name' => 'Instagram Brand',
            'access_token' => 'ig-token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'post_123',
            'message' => 'New Product Reveal! Comment LINK to get early access.',
        ]);

        $payload = [
            'name' => 'Product Reveal DM',
            'account_id' => $account->id,
            'target_type' => 'specific',
            'post_id' => $post->id,
            'keywords' => 'link, early access',
            'match_type' => 'contains',
            'dm_message' => 'Here is your VIP early access link: https://brand.com/vip',
            'is_active' => '1',
        ];

        $response = $this->actingAs($user)->post(route('admin.auto-dm.store'), $payload);

        $response->assertRedirect(route('admin.auto-dm.index'));

        $this->assertDatabaseHas('auto_dm_rules', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'post_id' => $post->id,
            'name' => 'Product Reveal DM',
        ]);
    }

    public function test_user_cannot_create_rule_for_another_users_account(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);

        $otherAccount = Account::create([
            'user_id' => $otherUser->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '999999',
            'name' => 'Unauthorized Page',
            'access_token' => 'token',
        ]);

        $payload = [
            'name' => 'Hacked Rule',
            'account_id' => $otherAccount->id,
            'target_type' => 'all',
            'keywords' => 'hack',
            'match_type' => 'contains',
            'dm_message' => 'Hacked',
        ];

        $response = $this->actingAs($user)->post(route('admin.auto-dm.store'), $payload);
        $response->assertStatus(403);
    }

    public function test_edit_endpoint_returns_json_for_rule_owner(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '54321',
            'name' => 'Brand Page',
            'access_token' => 'token',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Edit Me',
            'keywords' => ['edit', 'test'],
            'match_type' => 'exact',
            'dm_message' => 'Original Message',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.auto-dm.edit', $rule->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'rule' => [
                'id' => $rule->id,
                'name' => 'Edit Me',
                'keywords_string' => 'edit, test',
                'match_type' => 'exact',
                'dm_message' => 'Original Message',
                'is_active' => true,
            ]
        ]);
    }

    public function test_update_auto_dm_rule(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '54321',
            'name' => 'Brand Page',
            'access_token' => 'token',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Old Name',
            'keywords' => ['old'],
            'match_type' => 'contains',
            'dm_message' => 'Old DM',
            'is_active' => true,
        ]);

        $payload = [
            'account_id' => $account->id,
            'name' => 'Updated Rule Name',
            'target_type' => 'all',
            'keywords' => 'new, updated, promo',
            'match_type' => 'exact',
            'dm_message' => 'New Updated DM message',
            'public_reply' => 'Updated public reply',
            'is_active' => '1',
        ];

        $response = $this->actingAs($user)->put(route('admin.auto-dm.update', $rule->id), $payload);

        $response->assertRedirect(route('admin.auto-dm.index'));
        $rule->refresh();

        $this->assertEquals('Updated Rule Name', $rule->name);
        $this->assertEquals(['new', 'updated', 'promo'], $rule->keywords);
        $this->assertEquals('exact', $rule->match_type);
        $this->assertEquals('New Updated DM message', $rule->dm_message);
    }

    public function test_toggle_rule_status(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '54321',
            'name' => 'Brand Page',
            'access_token' => 'token',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Toggle Me',
            'keywords' => ['toggle'],
            'dm_message' => 'Msg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.auto-dm.toggle', $rule->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_active' => false,
        ]);

        $this->assertFalse($rule->fresh()->is_active);
    }

    public function test_delete_auto_dm_rule(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '54321',
            'name' => 'Brand Page',
            'access_token' => 'token',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Delete Me',
            'keywords' => ['del'],
            'dm_message' => 'Msg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->delete(route('admin.auto-dm.destroy', $rule->id));

        $response->assertRedirect(route('admin.auto-dm.index'));
        $this->assertDatabaseMissing('auto_dm_rules', ['id' => $rule->id]);
    }

    public function test_get_account_posts_endpoint(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '54321',
            'name' => 'Brand Page',
            'access_token' => 'token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'meta_p1',
            'message' => 'Hello World Announcement Post',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.auto-dm.account-posts', $account->id));

        $response->assertStatus(200);
        $response->assertJsonStructure(['posts' => [['id', 'title']]]);
        $this->assertEquals($post->id, $response->json('posts.0.id'));
    }
}
