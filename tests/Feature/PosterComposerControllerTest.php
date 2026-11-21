<?php

namespace Tests\Feature;

use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\PoliticalParty;
use App\Models\User;
use App\Models\UserBranding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PosterComposerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_guest_cannot_access_presets_or_compose(): void
    {
        $responsePresets = $this->getJson(route('admin.festival-studio.presets'));
        $responsePresets->assertStatus(401);

        $responseCompose = $this->postJson(route('admin.festival-studio.compose'), [
            'template_id' => 1,
        ]);
        $responseCompose->assertStatus(401);
    }

    public function test_authenticated_user_can_fetch_presets_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('admin.festival-studio.presets'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'presets' => [
                    '*' => [
                        'key',
                        'name',
                        'description',
                        'badge',
                        'leadership_mode',
                        'candidate_position',
                        'footer_style',
                    ],
                ],
            ]);

        $presets = $response->json('presets');
        $this->assertNotEmpty($presets);
        $keys = array_column($presets, 'key');
        $this->assertContains('classic_political_right', $keys);
        $this->assertContains('leader_tribute_royal', $keys);
    }

    public function test_compose_validates_required_template_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('admin.festival-studio.compose'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['template_id']);
    }

    public function test_authenticated_user_can_compose_branded_poster(): void
    {
        $party = PoliticalParty::firstOrCreate(
            ['short_code' => 'BJP'],
            [
                'name' => 'Bharatiya Janata Party',
                'primary_color' => '#FF9933',
                'secondary_color' => '#00873E',
                'group_cutout_image' => 'images/parties/bjp-group-cutout.png',
                'symbol_logo' => 'images/parties/bjp.jpeg',
                'is_active' => true,
            ]
        );

        $user = User::factory()->create(['name' => 'Rahul Sharma']);

        UserBranding::create([
            'user_id' => $user->id,
            'party_id' => $party->id,
            'display_name' => 'राहुल शर्मा',
            'designation' => 'जिला अध्यक्ष',
            'phone_number' => '+91 9999999999',
            'is_default' => true,
        ]);

        $festival = Festival::create([
            'title' => 'Gopashtami',
            'festival_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $template = FestivalTemplate::create([
            'festival_id' => $festival->id,
            'party_id' => $party->id,
            'title' => 'Gopashtami Special',
            'template_image_path' => 'images/templates/gopashtami.jpg',
            'preset_key' => 'classic_political_right',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.festival-studio.compose'), [
            'template_id'   => $template->id,
            'include_strip' => true,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'template_id' => $template->id,
            ])
            ->assertJsonStructure([
                'success',
                'image_url',
                'relative_path',
                'filename',
                'preset_used',
                'template_id',
            ]);

        $relativePath = $response->json('relative_path');
        $this->assertTrue(Storage::disk('public')->exists($relativePath));
    }

    public function test_compose_with_preset_override_and_strip_toggle(): void
    {
        $party = PoliticalParty::firstOrCreate(
            ['short_code' => 'BJP'],
            ['name' => 'Bharatiya Janata Party', 'is_active' => true]
        );

        $user = User::factory()->create();

        $festival = Festival::create([
            'title' => 'National Day',
            'festival_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $template = FestivalTemplate::create([
            'festival_id' => $festival->id,
            'party_id' => $party->id,
            'title' => 'National Day Tribute',
            'template_image_path' => 'images/templates/gopashtami.jpg',
            'preset_key' => 'classic_political_right',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.festival-studio.compose'), [
            'template_id'   => $template->id,
            'preset_key'    => 'leader_tribute_royal',
            'include_strip' => false,
        ]);

        $response->assertOk()
            ->assertJson([
                'success'     => true,
                'preset_used' => 'leader_tribute_royal',
            ]);
    }
}
