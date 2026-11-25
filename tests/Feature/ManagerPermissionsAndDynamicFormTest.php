<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerPermissionsAndDynamicFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_manager_without_client_type_and_with_custom_permissions(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Content Staff',
            'email' => 'content.staff@example.com',
            'role' => User::ROLE_MANAGER,
            'status' => User::STATUS_ACTIVE,
            // client_type omitted because Manager has no client profile type
            'can_manage_content' => '1',
            'can_manage_tickets' => '1',
            // can_manage_users and can_access_system unchecked
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $manager = User::where('email', 'content.staff@example.com')->first();
        $this->assertNotNull($manager);
        $this->assertNull($manager->client_type);
        $this->assertTrue($manager->can_manage_content);
        $this->assertTrue($manager->can_manage_tickets);
        $this->assertFalse($manager->can_manage_users);
        $this->assertFalse($manager->can_access_system);
    }

    public function test_creating_regular_user_still_requires_client_type(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Missing Type Client',
            'email' => 'missingtype@example.com',
            'role' => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $response->assertSessionHasErrors('client_type');
    }

    public function test_content_only_manager_can_access_templates_but_blocked_from_users_system_and_tickets(): void
    {
        $manager = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'client_type' => null,
            'can_manage_content' => true,
            'can_manage_users' => false,
            'can_manage_tickets' => false,
            'can_access_system' => false,
        ]);

        // Allowed: Political Parties, Festivals, Posters
        $this->actingAs($manager)->get(route('admin.political-parties.index'))->assertStatus(200);
        $this->actingAs($manager)->get(route('admin.festival-templates.index'))->assertStatus(200);
        $this->actingAs($manager)->get(route('admin.posters.index'))->assertStatus(200);

        // Forbidden: Users, System Activity, Webhook Logs, Support Tickets
        $this->actingAs($manager)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($manager)->get(route('admin.system-activity.index'))->assertStatus(403);
        $this->actingAs($manager)->get(route('admin.webhook-logs.index'))->assertStatus(403);
        $this->actingAs($manager)->get(route('admin.help.index'))->assertStatus(403);

        // Sidebar checks on Manager Dashboard
        $dash = $this->actingAs($manager)->get(route('admin.dashboard'));
        $dash->assertStatus(200);
        $dash->assertSee('Manager Workspace');
        $dash->assertSee('Political Parties</span>', false);
        $dash->assertSee('Festival Templates</span>', false);
        $dash->assertDontSee('All Users</span>', false);
        $dash->assertDontSee('System Activity</span>', false);
        $dash->assertDontSee('Support Tickets</span>', false);
        $dash->assertDontSee('Connected Accounts</span>', false);
    }

    public function test_user_manager_can_manage_clients_but_cannot_modify_admins(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $client = User::factory()->create(['role' => User::ROLE_USER]);
        $manager = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'client_type' => null,
            'can_manage_users' => true,
            'can_manage_content' => false,
            'can_manage_tickets' => false,
            'can_access_system' => false,
        ]);

        // Allowed to manage Users
        $this->actingAs($manager)->get(route('admin.users.index'))->assertStatus(200);
        $this->actingAs($manager)->get(route('admin.users.edit', $client->id))->assertStatus(200);

        // Blocked from editing or deleting Super Admin
        $this->actingAs($manager)->get(route('admin.users.edit', $admin->id))->assertStatus(403);
        $this->actingAs($manager)->deleteJson(route('admin.users.destroy', $admin->id))->assertStatus(403);

        // Blocked from content/system routes since unchecked
        $this->actingAs($manager)->get(route('admin.political-parties.index'))->assertStatus(403);
        $this->actingAs($manager)->get(route('admin.system-activity.index'))->assertStatus(403);
    }

    public function test_ticket_and_system_manager_permissions_grant_respective_access(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'VIP Voter']);
        $ticket = Ticket::create([
            'ticket_number' => 'TCK-MGR01',
            'user_id' => $client->id,
            'category' => Ticket::CATEGORY_TECHNICAL,
            'title' => 'Need verification help',
            'description' => 'Detailed technical request for account check.',
            'status' => Ticket::STATUS_OPEN,
        ]);

        $manager = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'client_type' => null,
            'can_manage_users' => false,
            'can_manage_content' => false,
            'can_manage_tickets' => true,
            'can_access_system' => true,
        ]);

        // System Activity & Webhook logs allowed
        $this->actingAs($manager)->get(route('admin.system-activity.index'))->assertStatus(200);
        $this->actingAs($manager)->get(route('admin.webhook-logs.index'))->assertStatus(200);

        // Support Tickets allowed to view & update status of client tickets
        $this->actingAs($manager)->getJson(route('admin.help.data'))
            ->assertStatus(200)
            ->assertSee('TCK-MGR01');

        $this->actingAs($manager)->patchJson(route('admin.help.update-status', $ticket->id), [
            'status' => Ticket::STATUS_RESOLVED,
            'admin_notes' => 'Resolved by Support Manager.',
        ])->assertStatus(200);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => Ticket::STATUS_RESOLVED,
            'admin_notes' => 'Resolved by Support Manager.',
        ]);
    }

    public function test_user_create_and_edit_pages_include_dynamic_role_sections_and_script(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $manager = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'client_type' => null,
            'can_manage_content' => true,
            'can_manage_users' => true,
        ]);

        // Create view has dynamic wrapper IDs & Manager Permissions card
        $createRes = $this->actingAs($admin)->get(route('admin.users.create'));
        $createRes->assertStatus(200);
        $createRes->assertSee('id="user-role-select"', false);
        $createRes->assertSee('id="client-type-wrapper"', false);
        $createRes->assertSee('id="plan-features-section"', false);
        $createRes->assertSee('id="manager-permissions-section"', false);
        $createRes->assertSee('Manage Users');
        $createRes->assertSee('System Access');

        // Edit view on existing Manager shows Manager Permissions active and hides Client Type & Plan Features initially
        $editRes = $this->actingAs($admin)->get(route('admin.users.edit', $manager->id));
        $editRes->assertStatus(200);
        $editRes->assertSee('col-12 mt-4 " id="manager-permissions-section"', false);
        $editRes->assertSee('col-12 mt-4 d-none" id="plan-features-section"', false);
        $editRes->assertSee('col-md-4 d-none" id="client-type-wrapper"', false);
    }
}
