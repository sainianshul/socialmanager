<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPlanFeaturesAndQuotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_feature_toggles_default_to_true(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $this->assertTrue($user->canSchedulePosts());
        $this->assertTrue($user->canUseFestivalStudio());
        $this->assertTrue($user->canUseAutoDm());
        $this->assertTrue($user->canUseAi());
        $this->assertFalse($user->hasExceededAiQuota());
    }

    public function test_admin_and_manager_always_have_all_features_enabled(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'can_schedule_posts' => false,
            'can_use_festival_studio' => false,
            'can_use_auto_dm' => false,
            'can_use_ai' => false,
            'ai_token_quota' => 10,
            'ai_tokens_used' => 500,
        ]);

        $this->assertTrue($admin->canSchedulePosts());
        $this->assertTrue($admin->canUseFestivalStudio());
        $this->assertTrue($admin->canUseAutoDm());
        $this->assertTrue($admin->canUseAi());
        $this->assertFalse($admin->hasExceededAiQuota());
    }

    public function test_user_cannot_access_festival_studio_when_disabled(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'can_use_festival_studio' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.festival-studio.index'));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_user_cannot_access_auto_dm_when_disabled(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'can_use_auto_dm' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.auto-dm.index'));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_user_cannot_access_schedule_create_when_disabled(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'can_schedule_posts' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.create'));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_user_cannot_generate_ai_caption_when_ai_disabled(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'can_use_ai' => false,
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.posts.generate-ai-caption'), [
            'topic' => 'Happy New Year Celebration',
        ]);

        $response->assertStatus(403);
        $response->assertJsonFragment(['success' => false]);
    }

    public function test_user_cannot_generate_ai_caption_when_quota_exceeded(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'can_use_ai' => true,
            'ai_token_quota' => 5000,
            'ai_tokens_used' => 5000,
            'ai_quota_resets_at' => now()->addDays(15),
        ]);

        $this->assertTrue($user->hasExceededAiQuota());

        $response = $this->actingAs($user)->postJson(route('admin.posts.generate-ai-caption'), [
            'topic' => 'Happy New Year Celebration',
        ]);

        $response->assertStatus(429);
        $response->assertJsonFragment(['success' => false]);
    }

    public function test_token_recording_increments_and_calculates_cost_and_percentage(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'ai_token_quota' => 100000,
            'ai_tokens_used' => 0,
        ]);

        $user->recordAiTokenUsage(25000);
        $user->refresh();

        $this->assertEquals(25000, $user->ai_tokens_used);
        $this->assertEquals(25.0, $user->aiQuotaPercentage());
        $this->assertEquals(0.30, $user->estimatedAiCostInr());
        $this->assertNotNull($user->ai_quota_resets_at);
    }

    public function test_lazy_quota_reset_resets_tokens_when_cycle_lapses(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'ai_token_quota' => 50000,
            'ai_tokens_used' => 50000,
            'ai_quota_resets_at' => now()->subDay(), // Lapsed yesterday
        ]);

        // hasExceededAiQuota should trigger auto-reset
        $this->assertFalse($user->hasExceededAiQuota());
        $user->refresh();

        $this->assertEquals(0, $user->ai_tokens_used);
        $this->assertTrue($user->ai_quota_resets_at->isFuture());
    }

    public function test_admin_can_create_user_with_plan_features_and_quota(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Custom Client',
            'email' => 'client@customplan.com',
            'phone' => '9876543219',
            'client_type' => User::CLIENT_TYPE_POLITICIAN,
            'role' => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
            'can_schedule_posts' => '1',
            'can_use_festival_studio' => '0',
            'can_use_auto_dm' => '1',
            'can_use_ai' => '0',
            'ai_token_quota' => '75000',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $newUser = User::where('email', 'client@customplan.com')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue($newUser->can_schedule_posts);
        $this->assertFalse($newUser->can_use_festival_studio);
        $this->assertTrue($newUser->can_use_auto_dm);
        $this->assertFalse($newUser->can_use_ai);
        $this->assertEquals(75000, $newUser->ai_token_quota);
    }

    public function test_admin_can_update_user_plan_features_and_quota(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'can_use_auto_dm' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'client_type' => $user->client_type ?: User::CLIENT_TYPE_POLITICIAN,
            'role' => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
            // Checkboxes unchecked (omitted in request)
            'ai_token_quota' => '200000',
        ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertFalse($user->can_use_auto_dm);
        $this->assertFalse($user->can_use_festival_studio);
        $this->assertEquals(200000, $user->ai_token_quota);
    }
}
