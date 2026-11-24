<?php

namespace Tests\Feature;

use App\Models\LeaderStrip;
use App\Models\LeaderStripItem;
use App\Models\PoliticalLeader;
use App\Models\PoliticalParty;
use App\Models\User;
use Database\Seeders\PoliticalLeaderSeeder;
use Database\Seeders\PoliticalPartySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoliticalLeaderStripTest extends TestCase
{
    use RefreshDatabase;

    public function test_political_leaders_and_strips_can_be_seeded(): void
    {
        $this->seed(PoliticalPartySeeder::class);
        $this->seed(PoliticalLeaderSeeder::class);

        $this->assertDatabaseHas('political_leaders', ['name' => 'Narendra Modi']);
        $this->assertDatabaseHas('political_leaders', ['name' => 'Yogi Adityanath']);
        $this->assertDatabaseHas('political_leaders', ['name' => 'Rahul Gandhi']);

        $bjp = PoliticalParty::where('short_code', 'BJP')->first();
        $this->assertNotNull($bjp);

        $defaultStrip = $bjp->defaultStrip;
        $this->assertNotNull($defaultStrip);
        $this->assertTrue($defaultStrip->is_system);
        $this->assertTrue($defaultStrip->is_default);

        $leaders = $defaultStrip->leaders;
        $this->assertCount(7, $leaders);
        $this->assertEquals('Narendra Modi', $leaders->first()->name);
        $this->assertEquals(1, $leaders->first()->pivot->order);
    }

    public function test_user_can_create_custom_leader_strip_with_hierarchy(): void
    {
        $this->seed(PoliticalPartySeeder::class);
        $this->seed(PoliticalLeaderSeeder::class);

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $bjp = PoliticalParty::where('short_code', 'BJP')->first();

        $modi = PoliticalLeader::where('name', 'Narendra Modi')->first();
        $yogi = PoliticalLeader::where('name', 'Yogi Adityanath')->first();

        // User creates their own strip putting Yogi first, then Modi
        $customStrip = LeaderStrip::create([
            'user_id' => $user->id,
            'party_id' => $bjp->id,
            'name' => 'My UP First Strip',
            'is_default' => true,
            'is_system' => false,
        ]);

        LeaderStripItem::create([
            'leader_strip_id' => $customStrip->id,
            'political_leader_id' => $yogi->id,
            'order' => 1,
        ]);

        LeaderStripItem::create([
            'leader_strip_id' => $customStrip->id,
            'political_leader_id' => $modi->id,
            'order' => 2,
        ]);

        $this->assertDatabaseHas('leader_strips', [
            'id' => $customStrip->id,
            'user_id' => $user->id,
            'name' => 'My UP First Strip',
            'is_default' => true,
        ]);

        $stripLeaders = $customStrip->leaders;
        $this->assertCount(2, $stripLeaders);
        $this->assertEquals('Yogi Adityanath', $stripLeaders->first()->name);
        $this->assertEquals(1, $stripLeaders->first()->pivot->order);
        $this->assertEquals('Narendra Modi', $stripLeaders->last()->name);
        $this->assertEquals(2, $stripLeaders->last()->pivot->order);
    }

    public function test_branding_studio_page_renders_with_nested_tabs_and_strips(): void
    {
        $this->seed(PoliticalPartySeeder::class);
        $this->seed(PoliticalLeaderSeeder::class);

        $user = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $bjp = PoliticalParty::where('short_code', 'BJP')->first();
        \App\Models\UserBranding::create([
            'user_id' => $user->id,
            'party_id' => $bjp->id,
            'display_name' => 'Dr. Test Leader',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user)->get(route('admin.branding.index'));

        $response->assertStatus(200);
        $response->assertSee('General Profile Information');
        $response->assertSee('Political Party Affiliation');
        $response->assertSee('Photo Cutout');
        $response->assertSee('Leadership Header Strips');

        $stripsResponse = $this->actingAs($user)->get(route('admin.leader-strips.index'));
        $stripsResponse->assertStatus(200);
        $stripsResponse->assertSee('Narendra Modi');
    }
}
