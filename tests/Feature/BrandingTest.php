<?php

namespace Tests\Feature;

use App\Models\BrandingImage;
use App\Models\PoliticalParty;
use App\Models\User;
use App\Models\UserBranding;
use Database\Seeders\PoliticalPartySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_branding_studio(): void
    {
        $response = $this->get(route('admin.branding.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_branding_studio(): void
    {
        $this->seed(PoliticalPartySeeder::class);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.branding.index'));

        $response->assertStatus(200);
        $response->assertSee('Branding Studio');
        $response->assertSee('Bharatiya Janata Party');
        $response->assertSee('Profile Category');
    }

    public function test_user_can_update_branding_profile_without_image(): void
    {
        $this->seed(PoliticalPartySeeder::class);
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'client_type' => User::CLIENT_TYPE_POLITICIAN,
        ]);
        $party = PoliticalParty::where('short_code', 'BJP')->first();

        $payload = [
            'display_name'     => 'डॉ. धर्म सिंह सैनी',
            'designation'      => 'पूर्व मंत्री (उत्तर प्रदेश)',
            'client_type'      => 'politician',
            'party_id'         => $party->id,
            'phone_number'     => '9876543210',
            'facebook_handle'  => 'Dharam.singhsaini',
            'instagram_handle' => 'Drdharamsinghsaini',
            'twitter_handle'   => 'Drdharamsaini',
        ];

        $response = $this->actingAs($user)->post(route('admin.branding.update'), $payload);

        $response->assertRedirect(route('admin.branding.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_brandings', [
            'user_id'          => $user->id,
            'display_name'     => 'डॉ. धर्म सिंह सैनी',
            'designation'      => 'पूर्व मंत्री (उत्तर प्रदेश)',
            'client_type'      => 'politician',
            'party_id'         => $party->id,
            'phone_number'     => '9876543210',
            'facebook_handle'  => 'Dharam.singhsaini',
            'instagram_handle' => 'Drdharamsinghsaini',
            'twitter_handle'   => 'Drdharamsaini',
        ]);
    }

    public function test_user_can_upload_branding_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $fakeImage = UploadedFile::fake()->image('cutout.png', 500, 500);

        $payload = [
            'display_name'   => 'Leader Name',
            'designation'    => 'MLA',
            'client_type'    => 'politician',
            'branding_image' => $fakeImage,
        ];

        $response = $this->actingAs($user)->post(route('admin.branding.update'), $payload);

        $response->assertRedirect(route('admin.branding.index'));
        $response->assertSessionHas('success');

        $branding = UserBranding::where('user_id', $user->id)->first();
        $this->assertNotNull($branding);

        $this->assertDatabaseHas('branding_images', [
            'user_id'          => $user->id,
            'user_branding_id' => $branding->id,
            'is_default'       => true,
        ]);

        $brandingImage = BrandingImage::where('user_id', $user->id)->first();
        $this->assertNotNull($brandingImage);
        Storage::disk('public')->assertExists($brandingImage->original_image_path);
    }

    public function test_party_id_is_null_when_client_type_is_business(): void
    {
        $this->seed(PoliticalPartySeeder::class);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $party = PoliticalParty::where('short_code', 'BJP')->first();

        $payload = [
            'display_name'     => 'Acme Enterprises',
            'designation'      => 'Founder & CEO',
            'client_type'      => 'business',
            'party_id'         => $party->id, // Provided but should be cleared for non-politicians
            'phone_number'     => '9876543210',
        ];

        $response = $this->actingAs($user)->post(route('admin.branding.update'), $payload);

        $response->assertRedirect(route('admin.branding.index'));

        $this->assertDatabaseHas('user_brandings', [
            'user_id'      => $user->id,
            'display_name' => 'Acme Enterprises',
            'client_type'  => 'business',
            'party_id'     => null,
        ]);

        $this->assertEquals('business', $user->fresh()->client_type);
    }

    public function test_validation_fails_on_missing_display_name(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $payload = [
            'display_name' => '',
            'client_type'  => 'politician',
        ];

        $response = $this->actingAs($user)->post(route('admin.branding.update'), $payload);

        $response->assertSessionHasErrors(['display_name']);
    }
}
