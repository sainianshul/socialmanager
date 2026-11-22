<?php

namespace Tests\Feature;

use App\Models\CardTemplate;
use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\PoliticalLeader;
use App\Models\PoliticalParty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPoliticalAndTemplatesManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;
    protected PoliticalParty $party;
    protected Festival $festival;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->regularUser = User::factory()->create([
            'role' => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->party = PoliticalParty::firstOrCreate(
            ['short_code' => 'BJP'],
            [
                'name' => 'Bharatiya Janata Party',
                'hindi_name' => 'भारतीय जनता पार्टी',
                'primary_color' => '#FF9933',
                'secondary_color' => '#138808',
                'is_active' => true,
            ]
        );

        $this->festival = Festival::firstOrCreate(
            ['title' => 'Tulsi Vivah'],
            [
                'festival_date' => now()->toDateString(),
                'category' => 'general',
                'is_active' => true,
            ]
        );
    }

    public function test_sidebar_renders_political_and_template_menus_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Political');
        $response->assertSee('Political Parties');
        $response->assertSee('Political Leaders');
        $response->assertSee('Leader Strips');
        $response->assertSee('Poster Templates');
        $response->assertSee('Festivals');
        $response->assertSee('Festival Templates');
        $response->assertSee('Card Templates');
        $response->assertSee('Poster Studio');
    }

    public function test_admin_can_list_political_parties(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.political-parties.index'));
        $response->assertStatus(200);
        $response->assertSee('Bharatiya Janata Party');
        $response->assertSee('BJP');
    }

    public function test_admin_can_create_political_party_with_file_upload(): void
    {
        $symbol = UploadedFile::fake()->image('rld_logo.png', 100, 100);
        $cutout = UploadedFile::fake()->image('cutout.png', 500, 200);

        $response = $this->actingAs($this->admin)->post(route('admin.political-parties.store'), [
            'name' => 'Rashtriya Lok Dal',
            'hindi_name' => 'राष्ट्रीय लोकदल',
            'short_code' => 'RLD',
            'primary_color' => '#008000',
            'secondary_color' => '#ffffff',
            'symbol_logo' => $symbol,
            'group_cutout_image' => $cutout,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.political-parties.index'));
        $this->assertDatabaseHas('political_parties', [
            'name' => 'Rashtriya Lok Dal',
            'short_code' => 'RLD',
        ]);

        $party = PoliticalParty::where('short_code', 'RLD')->first();
        $this->assertNotNull($party->symbol_logo);
        Storage::disk('public')->assertExists($party->symbol_logo);
    }

    public function test_admin_can_update_political_party(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.political-parties.update', $this->party), [
            'name' => 'Bharatiya Janata Party Updated',
            'hindi_name' => 'भाजपा',
            'short_code' => 'BJP',
            'primary_color' => '#FF9933',
            'secondary_color' => '#138808',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.political-parties.index'));
        $this->assertDatabaseHas('political_parties', [
            'id' => $this->party->id,
            'name' => 'Bharatiya Janata Party Updated',
            'hindi_name' => 'भाजपा',
        ]);
    }

    public function test_admin_can_delete_political_party(): void
    {
        $partyToDelete = PoliticalParty::create([
            'name' => 'Temporary Party',
            'short_code' => 'TMP',
            'primary_color' => '#112233',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.political-parties.destroy', $partyToDelete));
        $response->assertRedirect(route('admin.political-parties.index'));
        $this->assertDatabaseMissing('political_parties', ['id' => $partyToDelete->id]);
    }

    public function test_admin_can_list_and_create_political_leader(): void
    {
        $avatar = UploadedFile::fake()->image('modi.png', 120, 120);

        $response = $this->actingAs($this->admin)->post(route('admin.political-leaders.store'), [
            'party_id' => $this->party->id,
            'name' => 'Narendra Modi',
            'designation' => 'माननीय प्रधानमंत्री',
            'state' => 'National',
            'image' => $avatar,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.political-leaders.index', ['party_id' => $this->party->id]));
        $this->assertDatabaseHas('political_leaders', [
            'name' => 'Narendra Modi',
            'party_id' => $this->party->id,
        ]);

        $leader = PoliticalLeader::where('name', 'Narendra Modi')->first();
        $this->assertNotNull($leader->image);
        Storage::disk('public')->assertExists($leader->image);

        // Edit leader
        $editResponse = $this->actingAs($this->admin)->get(route('admin.political-leaders.edit', $leader));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Narendra Modi');

        // Delete leader
        $delResponse = $this->actingAs($this->admin)->delete(route('admin.political-leaders.destroy', $leader));
        $delResponse->assertRedirect();
        $this->assertDatabaseMissing('political_leaders', ['id' => $leader->id]);
    }

    public function test_admin_can_create_and_manage_festival_template(): void
    {
        $tplImage = UploadedFile::fake()->image('poster.jpg', 1080, 1080);

        $response = $this->actingAs($this->admin)->post(route('admin.festival-templates.store'), [
            'festival_id' => $this->festival->id,
            'party_id' => $this->party->id,
            'title' => 'Tulsi Vivah Official Celebration Poster',
            'preset_key' => 'classic_political_right',
            'photo_position' => 'bottom-right',
            'template_image' => $tplImage,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.festival-templates.index'));
        $this->assertDatabaseHas('festival_templates', [
            'title' => 'Tulsi Vivah Official Celebration Poster',
            'festival_id' => $this->festival->id,
            'preset_key' => 'classic_political_right',
        ]);

        $tpl = FestivalTemplate::where('title', 'Tulsi Vivah Official Celebration Poster')->first();
        Storage::disk('public')->assertExists($tpl->template_image_path);

        // Index view
        $indexResponse = $this->actingAs($this->admin)->get(route('admin.festival-templates.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Tulsi Vivah Official Celebration Poster');

        // Delete
        $delResponse = $this->actingAs($this->admin)->delete(route('admin.festival-templates.destroy', $tpl));
        $delResponse->assertRedirect(route('admin.festival-templates.index'));
        $this->assertDatabaseMissing('festival_templates', ['id' => $tpl->id]);
    }

    public function test_admin_can_create_and_manage_card_template(): void
    {
        $cardImage = UploadedFile::fake()->image('birthday_card.jpg', 1080, 1080);

        $response = $this->actingAs($this->admin)->post(route('admin.card-templates.store'), [
            'title' => 'Happy Birthday Leader Card',
            'category' => 'birthday',
            'party_id' => $this->party->id,
            'preset_key' => 'classic_political_right',
            'photo_position' => 'bottom-right',
            'template_image' => $cardImage,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.card-templates.index'));
        $this->assertDatabaseHas('card_templates', [
            'title' => 'Happy Birthday Leader Card',
            'category' => 'birthday',
        ]);

        $card = CardTemplate::where('title', 'Happy Birthday Leader Card')->first();
        Storage::disk('public')->assertExists($card->template_image_path);

        // Index view
        $indexResponse = $this->actingAs($this->admin)->get(route('admin.card-templates.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Happy Birthday Leader Card');

        // Update view
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.card-templates.update', $card), [
            'title' => 'Happy Birthday Leader Card Updated',
            'category' => 'birthday',
            'party_id' => $this->party->id,
            'preset_key' => 'candidate_left_classic',
            'photo_position' => 'bottom-left',
            'is_active' => '1',
        ]);
        $updateResponse->assertRedirect(route('admin.card-templates.index'));
        $this->assertDatabaseHas('card_templates', [
            'id' => $card->id,
            'title' => 'Happy Birthday Leader Card Updated',
            'preset_key' => 'candidate_left_classic',
        ]);

        // Delete
        $delResponse = $this->actingAs($this->admin)->delete(route('admin.card-templates.destroy', $card));
        $delResponse->assertRedirect(route('admin.card-templates.index'));
        $this->assertDatabaseMissing('card_templates', ['id' => $card->id]);
    }

    public function test_admin_can_create_and_manage_festivals(): void
    {
        // 1. Create festival
        $createResponse = $this->actingAs($this->admin)->post(route('admin.festivals.store'), [
            'title' => 'Khatu Shyam Janmotsav',
            'festival_date' => '2026-11-21',
            'category' => 'religious_festival',
            'ready_caption' => 'Jai Shri Shyam Ji Mahaparv Greetings',
            'hashtags' => '#KhatuShyam #ShyamBaba #KhatuDham',
            'is_active' => '1',
        ]);

        $createResponse->assertRedirect(route('admin.festivals.index'));
        $this->assertDatabaseHas('festivals', [
            'title' => 'Khatu Shyam Janmotsav',
            'category' => 'religious_festival',
            'is_active' => true,
        ]);

        $festival = Festival::where('title', 'Khatu Shyam Janmotsav')->first();
        $this->assertNotNull($festival);
        $this->assertEquals('2026-11-21', $festival->festival_date->format('Y-m-d'));

        // 2. Index listing
        $indexResponse = $this->actingAs($this->admin)->get(route('admin.festivals.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Festivals & Special Days');
        $indexResponse->assertSee('Khatu Shyam Janmotsav');

        // 3. Edit view
        $editViewResponse = $this->actingAs($this->admin)->get(route('admin.festivals.edit', $festival));
        $editViewResponse->assertStatus(200);
        $editViewResponse->assertSee('Edit Festival: Khatu Shyam Janmotsav');

        // 4. Update festival
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.festivals.update', $festival), [
            'title' => 'Khatu Shyam Janmotsav Grand Edition',
            'festival_date' => '2026-11-22',
            'category' => 'religious_festival',
            'ready_caption' => 'Updated Greeting Caption',
            'hashtags' => '#KhatuShyam2026',
            'is_active' => '1',
        ]);

        $updateResponse->assertRedirect(route('admin.festivals.index'));
        $this->assertDatabaseHas('festivals', [
            'id' => $festival->id,
            'title' => 'Khatu Shyam Janmotsav Grand Edition',
        ]);
        $this->assertEquals('2026-11-22', $festival->fresh()->festival_date->format('Y-m-d'));

        // 5. Delete festival
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.festivals.destroy', $festival));
        $deleteResponse->assertRedirect(route('admin.festivals.index'));
        $this->assertDatabaseMissing('festivals', ['id' => $festival->id]);
    }

    public function test_admin_can_preview_festival_template(): void
    {
        // Test preview with Layer 1 (dual_ribbon_badge)
        $response = $this->actingAs($this->admin)->postJson(route('admin.festival-templates.preview'), [
            'preset_key' => 'dual_ribbon_badge',
            'footer_style' => 'dual_ribbon_badge',
            'party_id' => $this->party->id,
            'leader_shape' => 'circle',
            'leader_border_color' => '#FF9933',
            'name_color' => '#ffffff',
            'photo_position' => 'bottom-right',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $response->json('preview_url'));

        // Test preview with Layer 2 (solid_white_banner)
        $response2 = $this->actingAs($this->admin)->postJson(route('admin.festival-templates.preview'), [
            'preset_key' => 'solid_white_banner',
            'footer_style' => 'solid_white_banner',
            'party_id' => $this->party->id,
            'leader_shape' => 'square',
            'leader_border_color' => '#D4AF37',
            'name_color' => '#FF6600',
            'photo_position' => 'bottom-right',
        ]);

        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
        ]);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $response2->json('preview_url'));

        // Test preview with Layer 3 (floating_tricolor_badge)
        $response3 = $this->actingAs($this->admin)->postJson(route('admin.festival-templates.preview'), [
            'preset_key' => 'floating_tricolor_badge',
            'footer_style' => 'floating_tricolor_badge',
            'party_id' => $this->party->id,
            'leader_shape' => 'rounded_square',
            'leader_border_color' => '#FF9933',
            'name_color' => '#ffffff',
            'photo_position' => 'bottom-right',
        ]);

        $response3->assertStatus(200);
        $response3->assertJson([
            'success' => true,
        ]);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $response3->json('preview_url'));

        // Test preview with Layer 4 (royal_gold_dock)
        $response4 = $this->actingAs($this->admin)->postJson(route('admin.festival-templates.preview'), [
            'preset_key' => 'royal_gold_dock',
            'footer_style' => 'royal_gold_dock',
            'party_id' => $this->party->id,
            'leader_shape' => 'square',
            'leader_border_color' => '#D4AF37',
            'name_color' => '#ffffff',
            'photo_position' => 'bottom-right',
        ]);

        $response4->assertStatus(200);
        $response4->assertJson([
            'success' => true,
        ]);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $response4->json('preview_url'));
    }

    public function test_admin_can_store_festival_template_with_layout_customizations(): void
    {
        $image = UploadedFile::fake()->image('custom_poster.jpg', 1080, 1080);

        $response = $this->actingAs($this->admin)->post(route('admin.festival-templates.store'), [
            'festival_id' => $this->festival->id,
            'party_id' => $this->party->id,
            'title' => 'Custom Saffron Layout Template',
            'preset_key' => 'dual_ribbon_badge',
            'footer_style' => 'dual_ribbon_badge',
            'photo_position' => 'bottom-right',
            'leader_shape' => 'rounded_square',
            'leader_border_color' => '#FF9933',
            'show_top_strip' => '1',
            'name_color' => '#ffffff',
            'footer_bg' => '#f8f5ee',
            'template_image' => $image,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.festival-templates.index'));

        $this->assertDatabaseHas('festival_templates', [
            'title' => 'Custom Saffron Layout Template',
            'preset_key' => 'dual_ribbon_badge',
            'photo_position' => 'bottom-right',
        ]);

        $template = FestivalTemplate::where('title', 'Custom Saffron Layout Template')->first();
        $this->assertNotNull($template);
        $this->assertEquals('dual_ribbon_badge', $template->layout_config['footer']['style']);
        $this->assertEquals('rounded_square', $template->layout_config['header']['chip_shape']);
        $this->assertEquals('#FF9933', $template->layout_config['header']['chip_border_color']);
        $this->assertEquals('#ffffff', $template->layout_config['footer']['name_color']);
    }

    public function test_regular_user_cannot_access_political_party_management(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.political-parties.index'));
        $response->assertForbidden();
    }
}
