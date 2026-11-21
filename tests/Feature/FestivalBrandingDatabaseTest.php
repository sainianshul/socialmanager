<?php

namespace Tests\Feature;

use App\Models\BrandingImage;
use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\PoliticalParty;
use App\Models\User;
use App\Models\UserBranding;
use Database\Seeders\FestivalSeeder;
use Database\Seeders\PoliticalPartySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FestivalBrandingDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_political_parties_can_be_seeded_and_queried(): void
    {
        $this->seed(PoliticalPartySeeder::class);

        $this->assertDatabaseHas('political_parties', ['short_code' => 'BJP']);
        $this->assertDatabaseHas('political_parties', ['short_code' => 'INC']);
        $this->assertDatabaseHas('political_parties', ['short_code' => 'AAP']);

        $bjp = PoliticalParty::where('short_code', 'BJP')->first();
        $this->assertNotNull($bjp);
        $this->assertEquals('#FF9933', $bjp->primary_color);
    }

    public function test_festivals_can_be_seeded_and_queried(): void
    {
        $this->seed(FestivalSeeder::class);

        $this->assertDatabaseHas('festivals', ['title' => 'Diwali - Deepawali Mahaparv']);
        $this->assertDatabaseHas('festivals', ['title' => 'Samvidhan Diwas (Constitution Day)']);

        $fest = Festival::where('title', 'Diwali - Deepawali Mahaparv')->first();
        $this->assertNotNull($fest);
        $this->assertStringContainsString('दीपावली', $fest->ready_caption);
    }

    public function test_user_branding_and_images_relationships(): void
    {
        $user = User::factory()->create(['client_type' => User::CLIENT_TYPE_POLITICIAN]);
        $party = PoliticalParty::create([
            'name' => 'Bharatiya Janata Party',
            'short_code' => 'BJP_TEST',
            'primary_color' => '#FF9933',
        ]);

        $branding = UserBranding::create([
            'user_id' => $user->id,
            'client_type' => User::CLIENT_TYPE_POLITICIAN,
            'party_id' => $party->id,
            'display_name' => 'Neta Test',
            'designation' => 'MLA Sanganer',
            'phone_number' => '9876543210',
            'is_default' => true,
        ]);

        $image = BrandingImage::create([
            'user_id' => $user->id,
            'user_branding_id' => $branding->id,
            'title' => 'Namaste Pose',
            'original_image_path' => 'brandings/photo1.jpg',
            'cutout_image_path' => 'brandings/photo1_cutout.png',
            'is_default' => true,
        ]);

        $this->assertEquals($party->id, $branding->party->id);
        $this->assertEquals($user->id, $branding->user->id);
        $this->assertCount(1, $user->brandings);
        $this->assertEquals($branding->id, $user->defaultBranding->id);
        $this->assertCount(1, $branding->images);
        $this->assertEquals('brandings/photo1_cutout.png', $branding->defaultImage->cutout_image_path);
    }

    public function test_festival_and_template_relationship(): void
    {
        $festival = Festival::create([
            'title' => 'Test Festival',
            'festival_date' => now()->addDays(5)->toDateString(),
            'category' => 'general',
            'ready_caption' => 'Happy Test Festival',
        ]);

        $party = PoliticalParty::create([
            'name' => 'Test Party',
            'short_code' => 'TP',
            'primary_color' => '#123456',
        ]);

        $template = FestivalTemplate::create([
            'festival_id' => $festival->id,
            'party_id' => $party->id,
            'title' => 'Template 1',
            'template_image_path' => 'templates/ram_navami_1.jpg',
            'photo_position' => 'bottom-right',
        ]);

        $this->assertCount(1, $festival->templates);
        $this->assertEquals('Template 1', $festival->templates->first()->title);
        $this->assertEquals($party->id, $template->party->id);
    }
}
