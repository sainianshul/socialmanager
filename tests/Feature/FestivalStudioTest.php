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

    public function test_quick_schedule_enforces_fixed_strip_and_customizes_leader_shape_and_color(): void
    {
        $this->seed(FestivalSeeder::class);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'name' => 'Official Facebook Page',
            'meta_account_id' => '1234567891',
            'access_token' => 'test-token',
            'is_active' => true,
        ]);

        $template = FestivalTemplate::first();
        $template->update([
            'layout_config' => [
                'footer' => [
                    'style' => 'royal_maroon_heritage',
                    'is_fixed' => true,
                ],
                'header' => [
                    'layout' => 'frosted_glass_dock',
                    'is_fixed' => true,
                    'chip_shape' => 'rounded_square',
                    'chip_border_color' => '#D4AF37',
                ],
            ],
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.festival-studio.quick-schedule'), [
            'template_id' => $template->id,
            'festival_id' => $template->festival_id,
            'account_ids' => [$account->id],
            'schedule_date' => '2026-11-26',
            'schedule_time' => '08:00',
            'content' => 'Message with fixed header & footer',
            'footer_style' => 'dual_ribbon_badge', // Attempt to override fixed footer
            'header_layout' => 'full_width_strip', // Attempt to override fixed header
            'leader_shape' => 'rounded_square',
            'leader_border_color' => '#D4AF37',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'template_id' => $template->id,
            'posts_count' => 1,
        ]);

        $post = ScheduledPost::where('user_id', $user->id)->first();
        $this->assertNotNull($post);
        $this->assertNotEmpty($post->media_urls);
        $this->assertNotEmpty($post->thumbnail_path);
    }

    public function test_leadership_strip_resolves_to_party_default_when_user_has_no_custom_strip(): void
    {
        $this->seed(FestivalSeeder::class);
        $this->seed(\Database\Seeders\PoliticalPartySeeder::class);
        $this->seed(\Database\Seeders\PoliticalLeaderSeeder::class);

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $bjp = \App\Models\PoliticalParty::where('short_code', 'BJP')->first();
        $inc = \App\Models\PoliticalParty::where('short_code', 'INC')->first();

        // 1. User with BJP branding and no custom strips gets BJP official system strip
        $branding = \App\Models\UserBranding::create([
            'user_id' => $user->id,
            'party_id' => $bjp->id,
            'display_name' => 'BJP Worker',
            'is_default' => true,
        ]);

        $template = FestivalTemplate::first();
        $template->update(['party_id' => null]); // Universal festival template (e.g. Diwali / New Year)

        $resolved = \App\Models\LeaderStrip::resolveFor($user, $template, $branding);

        $this->assertNotNull($resolved);
        $this->assertTrue($resolved->is_system);
        $this->assertEquals($bjp->id, $resolved->party_id);

        // 2. User with INC branding gets INC official system strip on universal template
        $branding->update(['party_id' => $inc->id]);
        $resolvedInc = \App\Models\LeaderStrip::resolveFor($user, $template, $branding);

        $this->assertNotNull($resolvedInc);
        $this->assertTrue($resolvedInc->is_system);
        $this->assertEquals($inc->id, $resolvedInc->party_id);
    }

    public function test_leadership_strip_resolves_to_user_custom_strip_when_user_has_added_their_own(): void
    {
        $this->seed(FestivalSeeder::class);
        $this->seed(\Database\Seeders\PoliticalPartySeeder::class);
        $this->seed(\Database\Seeders\PoliticalLeaderSeeder::class);

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $bjp = \App\Models\PoliticalParty::where('short_code', 'BJP')->first();

        \App\Models\UserBranding::create([
            'user_id' => $user->id,
            'party_id' => $bjp->id,
            'display_name' => 'Dr. Custom Leader',
            'is_default' => true,
        ]);

        // Create a custom strip for the user
        $customStrip = \App\Models\LeaderStrip::create([
            'user_id' => $user->id,
            'party_id' => $bjp->id,
            'name' => 'My Personal Constituency Leaders',
            'is_default' => true,
            'is_system' => false,
        ]);

        $leaders = \App\Models\PoliticalLeader::where('party_id', $bjp->id)->take(3)->get();
        foreach ($leaders as $idx => $leader) {
            \App\Models\LeaderStripItem::create([
                'leader_strip_id' => $customStrip->id,
                'political_leader_id' => $leader->id,
                'order' => $idx + 1,
            ]);
        }

        $template = FestivalTemplate::first();
        $resolved = \App\Models\LeaderStrip::resolveFor($user, $template);

        $this->assertNotNull($resolved);
        $this->assertEquals($customStrip->id, $resolved->id);
        $this->assertFalse($resolved->is_system);
        $this->assertEquals('My Personal Constituency Leaders', $resolved->name);
    }

    public function test_festival_studio_compose_and_quick_schedule_support_show_social_handles(): void
    {
        $this->seed(FestivalSeeder::class);
        $this->seed(\Database\Seeders\PoliticalPartySeeder::class);
        $this->seed(\Database\Seeders\PoliticalLeaderSeeder::class);

        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $bjp = \App\Models\PoliticalParty::where('short_code', 'BJP')->first();

        \App\Models\UserBranding::create([
            'user_id' => $user->id,
            'party_id' => $bjp->id,
            'display_name' => 'Rahul Sharma',
            'facebook_handle' => 'rahulsharma',
            'instagram_handle' => 'rahulsharma',
            'phone_number' => '9800000000',
            'is_default' => true,
        ]);

        $template = FestivalTemplate::first();

        // 1. Compose with show_social_handles = false
        $composeResponse = $this->actingAs($user)->postJson(route('admin.festival-studio.compose'), [
            'template_id' => $template->id,
            'footer_style' => 'dual_ribbon_badge',
            'show_social_handles' => false,
        ]);
        $composeResponse->assertStatus(200);
        $composeResponse->assertJson(['success' => true]);

        // 2. Compose with show_social_handles = true
        $composeResponseTrue = $this->actingAs($user)->postJson(route('admin.festival-studio.compose'), [
            'template_id' => $template->id,
            'footer_style' => 'dual_ribbon_badge',
            'show_social_handles' => true,
        ]);
        $composeResponseTrue->assertStatus(200);
        $composeResponseTrue->assertJson(['success' => true]);

        // 3. Verify modal blade has the toggle switch
        $indexResponse = $this->actingAs($user)->get(route('admin.festival-studio.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('id="qs-modal-show-social"', false);
        $indexResponse->assertSee('Social Media Handles');
    }

    public function test_festival_studio_supports_candidate_customization_and_photo_upload(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->seed(FestivalSeeder::class);
        $this->seed(\Database\Seeders\PoliticalPartySeeder::class);

        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $bjp = \App\Models\PoliticalParty::where('short_code', 'BJP')->first();

        \App\Models\UserBranding::create([
            'user_id' => $user->id,
            'party_id' => $bjp->id,
            'display_name' => 'Rahul Sharma',
            'designation' => 'Yuva Morcha Adhyaksh',
            'is_default' => true,
        ]);

        $template = FestivalTemplate::first();

        // 1. Quick Photo Upload
        $fakePhoto = \Illuminate\Http\UploadedFile::fake()->image('custom_cutout.png', 400, 400);
        $uploadResponse = $this->actingAs($user)->postJson(route('admin.festival-studio.upload-photo'), [
            'photo' => $fakePhoto,
        ]);

        $uploadResponse->assertStatus(200);
        $uploadResponse->assertJson(['success' => true]);
        $this->assertDatabaseHas('branding_images', [
            'user_id' => $user->id,
        ]);

        // 2. Compose with Custom Name & Designation
        $composeResponse = $this->actingAs($user)->postJson(route('admin.festival-studio.compose'), [
            'template_id' => $template->id,
            'candidate_name' => 'विक्रम सिंह राठौड़',
            'candidate_designation' => 'प्रदेश संयोजक',
        ]);
        $composeResponse->assertStatus(200);
        $composeResponse->assertJson(['success' => true]);

        // 3. Verify blade inputs exist
        $indexResponse = $this->actingAs($user)->get(route('admin.festival-studio.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('id="qs-modal-candidate-name"', false);
        $indexResponse->assertSee('id="qs-modal-candidate-designation"', false);
        $indexResponse->assertSee('id="qs-upload-photo-input"', false);
    }

    public function test_slide_panel_has_shortened_names_and_two_sections(): void
    {
        $this->seed(FestivalSeeder::class);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.festival-studio.index'));
        $response->assertStatus(200);

        // Verify Two Clean Sections
        $response->assertSee('Top Leadership Header');
        $response->assertSee('Branding Bottom Strip');

        // Verify Shortened Header and Footer Options
        $response->assertSee('<option value="full_width_strip">Full-Width Strip</option>', false);
        $response->assertSee('<option value="top_left_cluster">Top-Left Cluster</option>', false);
        $response->assertSee('<option value="dual_ribbon_badge">Dual Ribbon Badge</option>', false);
        $response->assertSee('<option value="solid_white_banner">Solid White Banner</option>', false);

        // Verify Candidate Details Collapsible
        $response->assertSee('id="panel-candidate-details-collapse"', false);
        $response->assertSee('id="panel-candidate-scale"', false);
        $response->assertSee('id="panel-leader-size"', false);
        $response->assertSee('id="panel-candidate-offset-x"', false);
        $response->assertSee('id="panel-candidate-offset-y"', false);
        $response->assertSee('id="panel-candidate-pos-reset"', false);
    }

    public function test_compose_accepts_and_bounds_candidate_position_offsets(): void
    {
        $this->seed(FestivalSeeder::class);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $template = FestivalTemplate::first();

        // Valid bounded position compose succeeds
        $okRes = $this->actingAs($user)->postJson(route('admin.festival-studio.compose'), [
            'template_id' => $template->id,
            'candidate_offset_x' => -75,
            'candidate_offset_y' => -40,
        ]);
        $okRes->assertStatus(200);
        $okRes->assertJson(['success' => true]);

        // Out-of-bounds offset fails validation safely
        $badRes = $this->actingAs($user)->postJson(route('admin.festival-studio.compose'), [
            'template_id' => $template->id,
            'candidate_offset_x' => 999,
            'candidate_offset_y' => -500,
        ]);
        $badRes->assertStatus(422);
        $badRes->assertJsonValidationErrors(['candidate_offset_x', 'candidate_offset_y']);
    }
}
