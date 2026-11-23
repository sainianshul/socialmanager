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
        $response->assertSee('Hindi Name');
        $response->assertSee('Bharatiya Janata Party');
        $response->assertSee('Political Party');
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
            'display_name'     => 'Dharam Singh Saini',
            'hindi_name'       => 'डॉ. धर्म सिंह सैनी',
            'designation'      => 'पूर्व मंत्री (उत्तर प्रदेश)',
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
            'display_name'     => 'Dharam Singh Saini',
            'hindi_name'       => 'डॉ. धर्म सिंह सैनी',
            'designation'      => 'पूर्व मंत्री (उत्तर प्रदेश)',
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

    public function test_navbar_contains_branding_studio_icon(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.branding.index'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.branding.index'));
        $response->assertSee('ti-user-circle');
    }

    public function test_validation_fails_on_missing_display_name(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $payload = [
            'display_name' => '',
        ];

        $response = $this->actingAs($user)->post(route('admin.branding.update'), $payload);

        $response->assertSessionHasErrors(['display_name']);
    }

    public function test_user_can_upload_multiple_branding_images_to_gallery(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        // Upload first image as default
        $response1 = $this->actingAs($user)->postJson(route('admin.branding.images.store'), [
            'branding_image' => UploadedFile::fake()->image('photo1.png', 400, 400),
            'is_default'     => true,
        ]);
        $response1->assertJson(['success' => true]);

        // Upload second image not default
        $response2 = $this->actingAs($user)->postJson(route('admin.branding.images.store'), [
            'branding_image' => UploadedFile::fake()->image('photo2.png', 400, 400),
            'is_default'     => false,
        ]);
        $response2->assertJson(['success' => true]);

        $this->assertEquals(2, $user->brandingImages()->count());
        $this->assertEquals(1, $user->brandingImages()->where('is_default', true)->count());
    }

    public function test_user_can_switch_default_branding_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $img1 = BrandingImage::create([
            'user_id'             => $user->id,
            'original_image_path' => 'uploads/branding/p1.png',
            'is_default'          => true,
        ]);

        $img2 = BrandingImage::create([
            'user_id'             => $user->id,
            'original_image_path' => 'uploads/branding/p2.png',
            'is_default'          => false,
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.branding.images.default', $img2->id));
        $response->assertJson(['success' => true]);

        $this->assertFalse($img1->fresh()->is_default);
        $this->assertTrue($img2->fresh()->is_default);
    }

    public function test_user_can_delete_branding_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $img1 = BrandingImage::create([
            'user_id'             => $user->id,
            'original_image_path' => 'uploads/branding/del1.png',
            'is_default'          => true,
        ]);

        $img2 = BrandingImage::create([
            'user_id'             => $user->id,
            'original_image_path' => 'uploads/branding/del2.png',
            'is_default'          => false,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('admin.branding.images.destroy', $img1->id));
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('branding_images', ['id' => $img1->id]);
        // Since default was deleted, img2 should automatically be set as default
        $this->assertTrue($img2->fresh()->is_default);
    }

    public function test_user_cannot_delete_another_users_branding_image(): void
    {
        $userA = User::factory()->create(['role' => User::ROLE_USER]);
        $userB = User::factory()->create(['role' => User::ROLE_USER]);

        $imgA = BrandingImage::create([
            'user_id'             => $userA->id,
            'original_image_path' => 'uploads/branding/userA.png',
            'is_default'          => true,
        ]);

        $response = $this->actingAs($userB)->deleteJson(route('admin.branding.images.destroy', $imgA->id));
        $response->assertStatus(403);
    }
}
