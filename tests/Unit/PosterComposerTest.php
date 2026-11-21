<?php

namespace Tests\Unit;

use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\LeaderStrip;
use App\Models\PoliticalLeader;
use App\Models\PoliticalParty;
use App\Models\User;
use App\Models\UserBranding;
use App\Services\PosterComposer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Interfaces\ImageInterface;
use Tests\TestCase;

class PosterComposerTest extends TestCase
{
    use RefreshDatabase;

    protected PosterComposer $composer;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->composer = new PosterComposer();
    }

    public function test_composer_instantiates_and_loads_presets(): void
    {
        $preset = $this->composer->getDefaultPresetConfig('classic_political_right');

        $this->assertIsArray($preset);
        $this->assertEquals(1080, $preset['canvas']['width']);
        $this->assertEquals('party_group_cutout', $preset['header']['mode']);
        $this->assertEquals('dual_ribbon', $preset['footer']['style']);

        $royalPreset = $this->composer->getDefaultPresetConfig('leader_tribute_royal');
        $this->assertEquals('royal_bar', $royalPreset['footer']['style']);
        $this->assertFalse($royalPreset['header']['enabled']);
    }

    public function test_composer_generates_image_with_minimal_inputs(): void
    {
        // Compose with dummy path
        $image = $this->composer->compose('non_existent_bg.jpg');

        $this->assertInstanceOf(ImageInterface::class, $image);
        $this->assertEquals(1080, $image->width());
        $this->assertEquals(1080, $image->height());
    }

    public function test_composer_generates_full_political_poster_with_branding(): void
    {
        $party = PoliticalParty::updateOrCreate(
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

        $branding = UserBranding::create([
            'user_id' => $user->id,
            'party_id' => $party->id,
            'display_name' => 'राहुल शर्मा',
            'designation' => 'जिला अध्यक्ष, युवा मोर्चा',
            'phone_number' => '+91 9876543210',
            'facebook_handle' => 'rahulsharmabjp',
            'instagram_handle' => 'rahul_bjp',
            'is_default' => true,
        ]);

        $festival = Festival::create([
            'title' => 'Ram Navami',
            'festival_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $template = FestivalTemplate::create([
            'festival_id' => $festival->id,
            'party_id' => $party->id,
            'title' => 'Ram Navami Classic Special',
            'template_image_path' => 'images/templates/gopashtami.jpg',
            'preset_key' => 'classic_political_right',
            'is_active' => true,
        ]);

        $result = $this->composer->composeToFile($template, $branding);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('relative_path', $result);
        $this->assertArrayHasKey('full_path', $result);
        $this->assertTrue(Storage::disk('public')->exists($result['relative_path']));
    }

    public function test_composer_renders_individual_chips_mode(): void
    {
        $party = PoliticalParty::firstOrCreate(
            ['short_code' => 'BJP'],
            ['name' => 'Bharatiya Janata Party', 'is_active' => true]
        );

        $user = User::factory()->create();

        $strip = LeaderStrip::create([
            'user_id' => $user->id,
            'party_id' => $party->id,
            'name' => 'My Core Leadership',
            'is_default' => true,
        ]);

        $leader1 = PoliticalLeader::create([
            'party_id' => $party->id,
            'name' => 'Narendra Modi',
            'image' => 'images/leaders/narendramodi.jpg',
            'is_active' => true,
        ]);

        $strip->leaders()->attach($leader1->id, ['order' => 1]);

        $branding = UserBranding::create([
            'user_id' => $user->id,
            'party_id' => $party->id,
            'display_name' => 'मुकेश कुमार',
            'designation' => 'सांसद प्रतिनिधि',
            'is_default' => true,
        ]);

        $image = $this->composer->compose(
            'images/templates/gopashtami.jpg',
            $branding,
            $strip,
            ['preset_key' => 'individual_circle_strip']
        );

        $this->assertInstanceOf(ImageInterface::class, $image);
        $this->assertEquals(1080, $image->width());
        $this->assertEquals(1080, $image->height());
    }

    public function test_composer_renders_royal_bar_footer(): void
    {
        $party = PoliticalParty::firstOrCreate(
            ['short_code' => 'BJP'],
            ['name' => 'Bharatiya Janata Party', 'is_active' => true]
        );

        $user = User::factory()->create();

        $branding = UserBranding::create([
            'user_id' => $user->id,
            'party_id' => $party->id,
            'display_name' => 'मुकेश राजपूत',
            'designation' => 'सांसद फर्रुखाबाद',
            'is_default' => true,
        ]);

        $image = $this->composer->compose(
            'images/templates/gopashtami.jpg',
            $branding,
            null,
            ['preset_key' => 'leader_tribute_royal']
        );

        $this->assertInstanceOf(ImageInterface::class, $image);
        $this->assertEquals(1080, $image->width());
    }

    public function test_composer_handles_disabled_strip_option(): void
    {
        $image = $this->composer->compose(
            'images/templates/gopashtami.jpg',
            null,
            null,
            ['include_strip' => false]
        );

        $this->assertInstanceOf(ImageInterface::class, $image);
        $this->assertEquals(1080, $image->width());
    }
}
