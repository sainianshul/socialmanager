<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_help_center_requires_authentication(): void
    {
        $response = $this->get(route('admin.help.index'));
        $response->assertRedirect('/admin/login');
    }

    public function test_help_center_renders_for_regular_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.help.index'));

        $response->assertStatus(200);
        $response->assertSee('Help Center &amp; Support Tickets', false);
        $response->assertSee('Add Ticket');
        $response->assertSee('Technical Issue');
        $response->assertSee('Social Account &amp; Sync', false);
    }

    public function test_user_can_submit_ticket(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $payload = [
            'category'    => Ticket::CATEGORY_TECHNICAL,
            'title'       => 'Meta webhook disconnection',
            'description' => 'Our Facebook Page token seems to be expired and comments are not syncing.',
            'priority'    => Ticket::PRIORITY_HIGH,
        ];

        $response = $this->actingAs($user)
            ->postJson(route('admin.help.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('tickets', [
            'user_id'  => $user->id,
            'category' => Ticket::CATEGORY_TECHNICAL,
            'title'    => 'Meta webhook disconnection',
            'priority' => Ticket::PRIORITY_HIGH,
            'status'   => Ticket::STATUS_OPEN,
        ]);

        $ticket = Ticket::first();
        $this->assertStringStartsWith('TCK-', $ticket->ticket_number);
    }

    public function test_ticket_validation_requires_mandatory_fields(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->postJson(route('admin.help.store'), [
            'category'    => 'invalid_cat',
            'title'       => '',
            'description' => 'short', // less than 10 chars
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'category', 'description']);
    }

    public function test_datatable_scopes_tickets_to_authenticated_user(): void
    {
        $userA = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'Alice']);
        $userB = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'Bob']);

        $ticketA = Ticket::create([
            'ticket_number' => 'TCK-ALICE1',
            'user_id'       => $userA->id,
            'category'      => Ticket::CATEGORY_TECHNICAL,
            'title'         => 'Alice Secret Ticket',
            'description'   => 'Alice description text here',
            'status'        => Ticket::STATUS_OPEN,
        ]);

        $ticketB = Ticket::create([
            'ticket_number' => 'TCK-BOB222',
            'user_id'       => $userB->id,
            'category'      => Ticket::CATEGORY_BILLING,
            'title'         => 'Bob Secret Ticket',
            'description'   => 'Bob description text here',
            'status'        => Ticket::STATUS_OPEN,
        ]);

        // Alice visits datatable data
        $response = $this->actingAs($userA)->getJson(route('admin.help.data'));

        $response->assertStatus(200);
        $response->assertSee('TCK-ALICE1');
        $response->assertSee('Alice Secret Ticket');

        // Alice must NOT see Bob's ticket
        $response->assertDontSee('TCK-BOB222');
        $response->assertDontSee('Bob Secret Ticket');
    }

    public function test_user_can_view_own_ticket_and_blocked_from_others(): void
    {
        $userA = User::factory()->create(['role' => User::ROLE_USER]);
        $userB = User::factory()->create(['role' => User::ROLE_USER]);

        $ticketA = Ticket::create([
            'ticket_number' => 'TCK-ALICE99',
            'user_id'       => $userA->id,
            'category'      => Ticket::CATEGORY_GENERAL,
            'title'         => 'Help needed with API',
            'description'   => 'Detailed description for help',
            'status'        => Ticket::STATUS_OPEN,
        ]);

        // User A views ticket A
        $resA = $this->actingAs($userA)->getJson(route('admin.help.show', $ticketA->id));
        $resA->assertStatus(200);
        $resA->assertJsonPath('ticket.ticket_number', 'TCK-ALICE99');

        // User B tries to view ticket A -> 403 Forbidden
        $resB = $this->actingAs($userB)->getJson(route('admin.help.show', $ticketA->id));
        $resB->assertStatus(403);
    }
}
