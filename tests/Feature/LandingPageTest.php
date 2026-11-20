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
        $response->assertSee('Scale Your Social Presence');
        $response->assertSee('100% on Autopilot');
        $response->assertSee('Automate multi-platform publishing');
        $response->assertSee('Facebook Pages');
        $response->assertSee('Instagram Professional');
        $response->assertSee('YouTube Channels');
        $response->assertSee('Why SocialManager');
        $response->assertSee('Unified Multi-Platform Content Scheduler');
        $response->assertSee('Adaptive AI Personas');
        $response->assertSee('Automated Comment-to-DM');
        $response->assertSee('24/7 Anti-Troll');
        $response->assertSee('Live Sentiment Intelligence');
        $response->assertSee('Up and Running in Under 5 Minutes');
        $response->assertSee('Connect Your Accounts');
        $response->assertSee('Configure AI Tone');
        $response->assertSee('Operate on Full Autopilot');
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('Can my Facebook, Instagram, or YouTube account get banned or restricted?');
        $response->assertSee('How does the Comment-to-DM lead capture trigger work on Instagram and Facebook?');
        $response->assertSee('Privacy Policy');
        $response->assertSee('Terms of Service');
        $response->assertSee('Data Deletion Policy');
        $response->assertDontSee('All APIs Operational');
        $response->assertDontSee('v2.4');
        $response->assertDontSee('Enterprise Social Media Infrastructure');
    }

    public function test_authenticated_user_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');
        $response->assertRedirect(route('admin.dashboard'));
    }
}
