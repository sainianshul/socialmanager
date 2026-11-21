<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_create_does_not_contain_admin_role_option(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        }

        $response = $this->actingAs($admin)->get(route('admin.users.create'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Check that Manager and User options are present in role select
        $this->assertStringContainsString('Manager</option>', $content);
        $this->assertStringContainsString('User</option>', $content);

        // Check that Admin option is NOT present in role select
        $this->assertStringNotContainsString('Admin</option>', $content);
    }

    public function test_user_store_rejects_admin_role(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        }

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Test Admin Attempt',
            'email' => 'hacker_admin@example.com',
            'password' => 'secret123',
            'client_type' => User::CLIENT_TYPE_POLITICIAN,
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        $response->assertSessionHasErrors('role');
    }

    public function test_user_store_requires_client_type(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        }

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secret123',
            'role' => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $response->assertSessionHasErrors('client_type');
    }

    public function test_user_store_succeeds_with_client_type(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        }

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Politician Leader',
            'email' => 'leader@example.com',
            'password' => 'secret123',
            'client_type' => User::CLIENT_TYPE_POLITICIAN,
            'role' => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'leader@example.com',
            'client_type' => User::CLIENT_TYPE_POLITICIAN,
        ]);
    }

    public function test_user_datatable_ajax_returns_email_column_and_cleans_name_column(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        }

        $response = $this->actingAs($admin)->getJson(route('admin.users.data'));

        $response->assertStatus(200);
        $json = $response->json();

        $this->assertArrayHasKey('data', $json);
        $this->assertNotEmpty($json['data']);

        $firstRow = $json['data'][0];

        // Should have 'email' column separately
        $this->assertArrayHasKey('email', $firstRow);

        // Name column should NOT contain email in small tag
        $this->assertStringNotContainsString('No email', $firstRow['name']);
        if (!empty($firstRow['email'])) {
            $this->assertStringNotContainsString('<small class="text-secondary">' . strip_tags($firstRow['email']) . '</small>', $firstRow['name']);
        }
    }

    public function test_user_index_view_has_email_header(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        }

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('<th>Email</th>', $content);
        $this->assertStringContainsString("{ data: 'email', name: 'email' }", $content);
    }
}

