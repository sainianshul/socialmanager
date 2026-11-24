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

    public function test_cannot_add_more_than_7_leaders_to_strip(): void
    {
        // Create 8 dummy leaders if needed
        $existing = PoliticalLeader::count();
        if ($existing < 8) {
            for ($i = $existing + 1; $i <= 8; $i++) {
                PoliticalLeader::create([
                    'party_id' => $this->bjp->id,
                    'name' => 'Leader ' . $i,
                    'designation' => 'Leader Designation ' . $i,
                    'is_active' => true,
                ]);
            }
        }

        $leaders = PoliticalLeader::take(8)->get();
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

    public function test_admin_can_create_multiple_system_strips_for_same_party(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $modi = PoliticalLeader::where('name', 'Narendra Modi')->first();

        // Multiple official strips for same party (e.g. BJP National, BJP Haryana) are permitted
        $response = $this->actingAs($admin)
            ->postJson(route('admin.leader-strips.store'), [
                'name' => 'BJP Haryana Official Strip',
                'party_id' => $this->bjp->id,
                'items' => [
                    ['leader_id' => $modi->id, 'order' => 1],
                ],
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('leader_strips', [
            'name' => 'BJP Haryana Official Strip',
            'party_id' => $this->bjp->id,
            'is_system' => true,
        ]);
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

    public function test_user_can_create_custom_local_leader(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('mla_photo.jpg', 400, 400);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.leader-strips.custom-leader.store'), [
                'name' => 'Dr. Ramesh Sharma (MLA)',
                'designation' => 'MLA - Cantt Constituency',
                'party_id' => $this->bjp->id,
                'image' => $file,
            ]);

        $response->assertStatus(201);
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals('Dr. Ramesh Sharma (MLA)', $data['leader']['name']);
        $this->assertTrue($data['leader']['is_custom']);

        $this->assertDatabaseHas('political_leaders', [
            'name' => 'Dr. Ramesh Sharma (MLA)',
            'user_id' => $this->user->id,
            'party_id' => $this->bjp->id,
            'state' => 'Local',
        ]);
    }

    public function test_custom_leader_appears_for_owner_and_hidden_for_other_users(): void
    {
        $customLeader = PoliticalLeader::create([
            'user_id' => $this->user->id,
            'party_id' => $this->bjp->id,
            'name' => 'Local Zila Adhyaksh',
            'designation' => 'District President',
            'state' => 'Local',
            'is_active' => true,
        ]);

        // Owner can see the custom leader
        $resOwner = $this->actingAs($this->user)
            ->getJson(route('admin.leader-strips.leaders', ['party_id' => $this->bjp->id]));
        $resOwner->assertStatus(200);
        $names = collect($resOwner->json('leaders'))->pluck('name')->all();
        $this->assertContains('Local Zila Adhyaksh', $names);

        // Another user cannot see this custom leader
        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
        $resOther = $this->actingAs($otherUser)
            ->getJson(route('admin.leader-strips.leaders', ['party_id' => $this->bjp->id]));
        $resOther->assertStatus(200);
        $otherNames = collect($resOther->json('leaders'))->pluck('name')->all();
        $this->assertNotContains('Local Zila Adhyaksh', $otherNames);
    }

    public function test_user_can_delete_own_custom_leader_but_not_others(): void
    {
        $customLeader = PoliticalLeader::create([
            'user_id' => $this->user->id,
            'party_id' => $this->bjp->id,
            'name' => 'Temporary Leader',
            'state' => 'Local',
            'is_active' => true,
        ]);

        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);

        // Other user fails to delete
        $otherRes = $this->actingAs($otherUser)
            ->deleteJson(route('admin.leader-strips.custom-leader.destroy', $customLeader->id));
        $otherRes->assertStatus(404);

        // Owner successfully deletes
        $ownerRes = $this->actingAs($this->user)
            ->deleteJson(route('admin.leader-strips.custom-leader.destroy', $customLeader->id));
        $ownerRes->assertStatus(200);
        $this->assertDatabaseMissing('political_leaders', ['id' => $customLeader->id]);
    }
}