<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\ScheduledPost;
use App\Models\User;
use Database\Seeders\FestivalSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FestivalStudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_festival_studio_requires_authentication(): void
    {
        $response = $this->get(route('admin.festival-studio.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_festival_studio(): void
    {
        $this->seed(FestivalSeeder::class);

        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.festival-studio.index'));

        $response->assertStatus(200);
        $response->assertSee('Festival Studio');
        $response->assertSee('Samvidhan Diwas');
    }

    public function test_sidebar_contains_festival_studio_for_regular_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.posts.create'));

        $response->assertStatus(200);
        $response->assertSee('Festival Studio');
        $response->assertSee(route('admin.festival-studio.index'));
    }

    public function test_festival_studio_ajax_returns_json_content(): void
    {
        $this->seed(FestivalSeeder::class);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $festival = Festival::where('festival_date', '>=', now()->toDateString())->first();

        $response = $this->actingAs($user)->getJson(
            route('admin.festival-studio.index', ['festival_id' => $festival->id]),
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'festival_id' => $festival->id,
            'title' => $festival->title,
        ]);
        $response->assertJsonStructure([
            'success',
            'festival_id',
            'title',
            'festival_date',
            'caption',
            'hashtags',
            'html',
        ]);
    }

    public function test_quick_schedule_creates_scheduled_post(): void
    {
        $this->seed(FestivalSeeder::class);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'name' => 'Official Leader Page',
            'meta_account_id' => '1234567890',
            'access_token' => 'test-token',
            'is_active' => true,
        ]);

        $template = FestivalTemplate::first();
        $festival = $template->festival;

        $response = $this->actingAs($user)->postJson(route('admin.festival-studio.quick-schedule'), [
            'template_id' => $template->id,
            'festival_id' => $festival?->id,
            'account_ids' => [$account->id],
            'schedule_date' => '2026-11-26',
            'schedule_time' => '08:00',
            'content' => 'Samvidhan Diwas Shubhkaamna message',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'template_id' => $template->id,
            'posts_count' => 1,
        ]);

        $this->assertDatabaseHas('scheduled_posts', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'content' => 'Samvidhan Diwas Shubhkaamna message',
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        $scheduledPost = ScheduledPost::where('user_id', $user->id)
            ->where('account_id', $account->id)
            ->first();

        $this->assertNotNull($scheduledPost);
        $this->assertNotEmpty($scheduledPost->media_urls);
        $this->assertStringContainsString('posters/', $scheduledPost->media_urls[0]);
    }

    public function test_quick_schedule_for_today_sets_time_10_minutes_ahead(): void
    {
        $this->seed(FestivalSeeder::class);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'name' => 'Official Insta',
            'meta_account_id' => '987654321',
            'access_token' => 'test-token',
            'is_active' => true,
        ]);

        $template = FestivalTemplate::first();

        $response = $this->actingAs($user)->postJson(route('admin.festival-studio.quick-schedule'), [
            'template_id' => $template->id,
            'festival_id' => $template->festival_id,
            'account_ids' => [$account->id],
            'schedule_date' => now()->toDateString(),
            'schedule_time' => '08:00',
            'content' => 'Today festival greeting',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'template_id' => $template->id,
            'posts_count' => 1,
        ]);

        $scheduledPost = ScheduledPost::where('user_id', $user->id)
            ->where('account_id', $account->id)
            ->first();

        $this->assertNotNull($scheduledPost);
        $this->assertTrue($scheduledPost->scheduled_at->isFuture());
    }

    public function test_festival_studio_renders_clean_templates_without_card_overlays(): void
    {
        $this->seed(FestivalSeeder::class);
        $this->seed(\Database\Seeders\PoliticalPartySeeder::class);
        $this->seed(\Database\Seeders\PoliticalLeaderSeeder::class);

        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $bjp = \App\Models\PoliticalParty::where('short_code', 'BJP')->first();

        \App\Models\UserBranding::create([
            'user_id' => $user->id,
            'party_id' => $bjp->id,
            'display_name' => 'Dr. Leader Test',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user)->get(route('admin.festival-studio.index'));

        $response->assertStatus(200);
        $response->assertDontSee('id="fs-toggle-header-strip"');
        $response->assertSee('fs-template-preview-img');
        $response->assertSee('Festival Studio');
    }
}
