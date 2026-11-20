<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_landing_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Hopewell');
        $response->assertSee('Give Hope, Change Lives');
        $response->assertSee('Active Campaigns');
        $response->assertSee('Causes waiting for you');
        $response->assertSee('Why Hopewell');
        $response->assertSee('How your gift travels');
        $response->assertSee('Our People');
        $response->assertSee('Stories from our giving community');
        $response->assertSee('Get In Touch');
    }

    public function test_authenticated_user_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');
        $response->assertRedirect(route('admin.dashboard'));
    }
}
