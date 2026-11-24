<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ScheduledPost;
use App\Models\SystemActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserShowTabsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'   => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->regularUser = User::factory()->create([
            'role'   => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
            'name'   => 'Test Client User',
        ]);
    }

    public function test_admin_can_view_user_profile_with_new_tabs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.show', $this->regularUser->id));

        $response->assertStatus(200);
        $response->assertSee('Connected Accounts');
        $response->assertSee('Posts & Content', false);
        $response->assertSee('User Activity');
        $response->assertSee('Devices & Sessions', false);
    }

    public function test_admin_can_fetch_user_accounts_ajax_data(): void
    {
        Account::create([
            'user_id'      => $this->regularUser->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'name'         => 'Client FB Page',
            'access_token' => 'token_123',
            'is_active'    => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.accounts-data', $this->regularUser->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
        $this->assertStringContainsString('Client FB Page', json_encode($response->json()));
    }

    public function test_admin_can_fetch_user_posts_ajax_data(): void
    {
        $account = Account::create([
            'user_id'      => $this->regularUser->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'name'         => 'Client YT Channel',
            'access_token' => 'token_456',
            'is_active'    => true,
        ]);

        ScheduledPost::create([
            'user_id'      => $this->regularUser->id,
            'account_id'   => $account->id,
            'platform'     => Account::TYPE_YOUTUBE,
            'post_type'    => 'video',
            'title'        => 'My First Client Video',
            'content'      => 'Video description',
            'status'       => ScheduledPost::STATUS_PUBLISHED,
            'scheduled_at' => now(),
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.posts-data', $this->regularUser->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
        $this->assertStringContainsString('My First Client Video', json_encode($response->json()));
    }

    public function test_admin_can_fetch_user_activity_ajax_data(): void
    {
        SystemActivityLog::create([
            'user_id'    => $this->regularUser->id,
            'event_type' => SystemActivityLog::EVENT_POST_PUBLISH,
            'action'     => 'Uploaded client test video',
            'status'     => SystemActivityLog::STATUS_SUCCESS,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.activity-data', $this->regularUser->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
        $this->assertStringContainsString('Uploaded client test video', json_encode($response->json()));
    }

    public function test_regular_user_cannot_access_user_tabs_data(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.users.activity-data', $this->regularUser->id));

        $response->assertStatus(403);
    }
}
