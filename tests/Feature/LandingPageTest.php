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
        $response->assertSee('SocialManager');
        $response->assertSee('Publishing Schedule Calendar');
        $response->assertSee('Live AI Comment Moderation');
        $response->assertSee('Auto DM Dispatched');
        $response->assertSee('Facebook Pages');
        $response->assertSee('Instagram Professional');
        $response->assertSee('YouTube Channels');
        $response->assertSee('Unified Multi-Platform Content Scheduler');
        $response->assertSee('Adaptive AI Personas');
        $response->assertSee('Up and Running in Under 5 Minutes');
        $response->assertSee('Connect Your Accounts');
        $response->assertSee('Operate on Full Autopilot');
        $response->assertSee('See SocialManager in Action');
        $response->assertSee('AI Comment Threading');
        $response->assertSee('Comment-to-DM Flow');
        $response->assertSee('Anti-Troll Shield');
    }

    public function test_authenticated_user_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');
        $response->assertRedirect(route('admin.dashboard'));
    }
}
