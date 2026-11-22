<?php

namespace Tests\Feature;

use App\Models\LeaderStrip;
use App\Models\PoliticalLeader;
use App\Models\PoliticalParty;
use App\Models\User;
use Database\Seeders\PoliticalLeaderSeeder;
use Database\Seeders\PoliticalPartySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderStripControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private PoliticalParty $bjp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PoliticalPartySeeder::class);
        $this->seed(PoliticalLeaderSeeder::class);

        $this->user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $this->bjp = PoliticalParty::where('short_code', 'BJP')->first();
    }

    public function test_can_list_strips_including_system_and_user_strips(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('admin.leader-strips.index', ['party_id' => $this->bjp->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['system_strips']);
        $this->assertEquals('BJP Uttar Pradesh & Central Leadership', $data['system_strips'][0]['name']);
        $this->assertNotNull($data['active_strip']);
    }

    public function test_can_fetch_leaders_with_search_and_filter(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('admin.leader-strips.leaders', [
                'party_id' => $this->bjp->id,
                'search' => 'Modi',
            ]));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertCount(1, $data['leaders']);
        $this->assertEquals('Narendra Modi', $data['leaders'][0]['name']);
    }

    public function test_can_create_custom_strip_with_ordered_leaders(): void
    {
        $modi = PoliticalLeader::where('name', 'Narendra Modi')->first();
        $yogi = PoliticalLeader::where('name', 'Yogi Adityanath')->first();

        $payload = [
            'name' => 'My UP & National Strip',
            'party_id' => $this->bjp->id,
            'is_default' => true,
            'items' => [
                ['leader_id' => $yogi->id, 'order' => 1],
                ['leader_id' => $modi->id, 'order' => 2],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.leader-strips.store'), $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Header strip created successfully.',
        ]);

        $stripId = $response->json('strip.id');
        $this->assertDatabaseHas('leader_strips', [
            'id' => $stripId,
            'user_id' => $this->user->id,
            'name' => 'My UP & National Strip',
            'is_default' => true,
        ]);

        $this->assertDatabaseHas('leader_strip_items', [
            'leader_strip_id' => $stripId,
            'political_leader_id' => $yogi->id,
            'order' => 1,
        ]);

        $this->assertDatabaseHas('leader_strip_items', [
            'leader_strip_id' => $stripId,
            'political_leader_id' => $modi->id,
            'order' => 2,
        ]);
    }

    public function test_can_update_custom_strip(): void
    {
        $modi = PoliticalLeader::where('name', 'Narendra Modi')->first();
        $yogi = PoliticalLeader::where('name', 'Yogi Adityanath')->first();

        $strip = LeaderStrip::create([
            'user_id' => $this->user->id,
            'party_id' => $this->bjp->id,
            'name' => 'Initial Strip',
            'is_default' => false,
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('admin.leader-strips.update', $strip->id), [
                'name' => 'Updated Custom Strip',
                'is_default' => true,
                'leader_ids' => [$modi->id, $yogi->id],
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Header strip updated successfully.',
        ]);

        $this->assertDatabaseHas('leader_strips', [
            'id' => $strip->id,
            'name' => 'Updated Custom Strip',
            'is_default' => true,
        ]);
    }

    public function test_can_toggle_default_strip(): void
    {
        $strip1 = LeaderStrip::create([
            'user_id' => $this->user->id,
            'party_id' => $this->bjp->id,
            'name' => 'Strip 1',
            'is_default' => true,
            'is_system' => false,
        ]);

        $strip2 = LeaderStrip::create([
            'user_id' => $this->user->id,
            'party_id' => $this->bjp->id,
            'name' => 'Strip 2',
            'is_default' => false,
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.leader-strips.default', $strip2->id));

        $response->assertStatus(200);
        $this->assertTrue($strip2->fresh()->is_default);
        $this->assertFalse($strip1->fresh()->is_default);
    }

    public function test_can_delete_custom_strip(): void
    {
        $strip = LeaderStrip::create([
            'user_id' => $this->user->id,
            'party_id' => $this->bjp->id,
            'name' => 'To Delete',
            'is_default' => false,
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('admin.leader-strips.destroy', $strip->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('leader_strips', ['id' => $strip->id]);
    }

    public function test_cannot_delete_system_strip(): void
    {
        $systemStrip = LeaderStrip::where('is_system', true)->first();
        $this->assertNotNull($systemStrip);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('admin.leader-strips.destroy', $systemStrip->id));

        $response->assertStatus(404);
        $this->assertDatabaseHas('leader_strips', ['id' => $systemStrip->id]);
    }

    public function test_can_render_leader_strips_web_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.leader-strips.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.leader_strips.index');
        $response->assertSee('Leadership Strips');
        $response->assertSee('Create Strip');
    }

    public function test_cannot_add_more_than_6_leaders_to_strip(): void
    {
        $leaders = PoliticalLeader::take(7)->get();
        $items = $leaders->map(fn($l, $idx) => [
            'leader_id' => $l->id,
            'order' => $idx + 1,
        ])->toArray();

        $payload = [
            'name' => 'Too Many Leaders Strip',
            'party_id' => $this->bjp->id,
            'items' => $items,
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.leader-strips.store'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items']);
    }

    public function test_admin_can_render_official_strips_page_with_add_button(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->get(route('admin.leader-strips.index'));

        $response->assertStatus(200);
        $response->assertSee('Political Leadership Strips');
        $response->assertSee('Add Leader Strip');
        $response->assertDontSee('Personal Strip');
    }

    public function test_admin_can_create_official_system_strip(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $newParty = PoliticalParty::firstOrCreate(
            ['short_code' => 'DEMO_P'],
            [
                'name' => 'Demo Political Party',
                'primary_color' => '#0055A5',
                'secondary_color' => '#ffffff',
                'is_active' => true,
            ]
        );
        $leader = PoliticalLeader::create([
            'party_id' => $newParty->id,
            'name' => 'Demo Leader',
            'designation' => 'Party President',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.leader-strips.store'), [
                'name' => 'Demo Official Strip',
                'party_id' => $newParty->id,
                'items' => [
                    ['leader_id' => $leader->id, 'order' => 1],
                ],
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Official party leadership strip created successfully.',
        ]);

        $this->assertDatabaseHas('leader_strips', [
            'party_id' => $newParty->id,
            'is_system' => true,
            'user_id' => null,
            'name' => 'Demo Official Strip',
        ]);
    }

    public function test_admin_cannot_create_duplicate_system_strip_for_same_party(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $modi = PoliticalLeader::where('name', 'Narendra Modi')->first();

        // BJP already has a system strip from PoliticalPartySeeder
        $response = $this->actingAs($admin)
            ->postJson(route('admin.leader-strips.store'), [
                'name' => 'Duplicate BJP Strip',
                'party_id' => $this->bjp->id,
                'items' => [
                    ['leader_id' => $modi->id, 'order' => 1],
                ],
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['party_id']);
    }

    public function test_admin_can_update_and_delete_official_system_strip(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $systemStrip = LeaderStrip::where('is_system', true)->first();
        $this->assertNotNull($systemStrip);

        // Admin updates official strip
        $modi = PoliticalLeader::where('name', 'Narendra Modi')->first();
        $updateRes = $this->actingAs($admin)
            ->putJson(route('admin.leader-strips.update', $systemStrip->id), [
                'name' => 'Updated Official Strip Name',
                'party_id' => $systemStrip->party_id,
                'items' => [
                    ['leader_id' => $modi->id, 'order' => 1],
                ],
            ]);

        $updateRes->assertStatus(200);
        $this->assertDatabaseHas('leader_strips', [
            'id' => $systemStrip->id,
            'name' => 'Updated Official Strip Name',
        ]);

        // Admin deletes official strip
        $deleteRes = $this->actingAs($admin)
            ->deleteJson(route('admin.leader-strips.destroy', $systemStrip->id));

        $deleteRes->assertStatus(200);
        $this->assertDatabaseMissing('leader_strips', ['id' => $systemStrip->id]);
    }
}