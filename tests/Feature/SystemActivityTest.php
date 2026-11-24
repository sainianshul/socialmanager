<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\SystemActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\QueueMonitorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SystemActivityTest extends TestCase
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
        ]);
    }

    public function test_admin_can_access_system_activity_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.system-activity.index'));

        $response->assertStatus(200);
        $response->assertSee('System Activity &amp; Queue Monitor', false);
        $response->assertSee('Pending Queue Jobs');
        $response->assertSee('Failed Queue Jobs');
    }

    public function test_regular_user_cannot_access_system_activity(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.system-activity.index'));

        $response->assertStatus(403);
    }

    public function test_activity_logger_records_logs_safely(): void
    {
        $account = Account::create([
            'user_id'      => $this->admin->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'name'         => 'Official Test Page',
            'access_token' => 'test_access_token_123',
            'is_active'    => true,
        ]);

        $log = ActivityLogger::log(
            SystemActivityLog::EVENT_ACCOUNT_SYNC,
            "Synced Facebook Page '{$account->name}'",
            SystemActivityLog::STATUS_SUCCESS,
            ['page_id' => '12345678'],
            ['followers' => 5000],
            145,
            $account->id,
            $this->admin->id
        );

        $this->assertNotNull($log);
        $this->assertDatabaseHas('system_activity_logs', [
            'event_type' => SystemActivityLog::EVENT_ACCOUNT_SYNC,
            'status'     => SystemActivityLog::STATUS_SUCCESS,
            'account_id' => $account->id,
            'user_id'    => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.system-activity.index'));
        $response->assertStatus(200);
        $response->assertSee("Synced Facebook Page &#039;{$account->name}&#039;", false);
    }

    public function test_logs_can_be_filtered_by_event_type_and_status(): void
    {
        SystemActivityLog::create([
            'user_id'    => $this->admin->id,
            'event_type' => SystemActivityLog::EVENT_ACCOUNT_SYNC,
            'action'     => 'FB Sync Action',
            'status'     => SystemActivityLog::STATUS_SUCCESS,
        ]);

        SystemActivityLog::create([
            'user_id'    => $this->admin->id,
            'event_type' => SystemActivityLog::EVENT_POST_PUBLISH,
            'action'     => 'Failed Post Action',
            'status'     => SystemActivityLog::STATUS_FAILED,
            'response'   => 'OAuth token invalid',
        ]);

        // Filter for post_publish
        $response = $this->actingAs($this->admin)->get(route('admin.system-activity.index', [
            'event_type' => SystemActivityLog::EVENT_POST_PUBLISH,
        ]));
        $response->assertStatus(200);
        $response->assertSee('Failed Post Action');
        $response->assertDontSee('FB Sync Action');

        // Filter for failed
        $response = $this->actingAs($this->admin)->get(route('admin.system-activity.index', [
            'status' => 'failed',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Failed Post Action');
        $response->assertDontSee('FB Sync Action');
    }

    public function test_logs_can_be_filtered_by_user_id(): void
    {
        $log1 = SystemActivityLog::create([
            'user_id'    => $this->admin->id,
            'event_type' => SystemActivityLog::EVENT_POST_PUBLISH,
            'action'     => 'Action by SuperAdmin',
            'status'     => SystemActivityLog::STATUS_SUCCESS,
        ]);

        $log2 = SystemActivityLog::create([
            'user_id'    => $this->regularUser->id,
            'event_type' => SystemActivityLog::EVENT_POST_PUBLISH,
            'action'     => 'Action by Client User',
            'status'     => SystemActivityLog::STATUS_SUCCESS,
        ]);

        // Filter by regular user id
        $response = $this->actingAs($this->admin)->get(route('admin.system-activity.index', [
            'user_id' => $this->regularUser->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Action by Client User');
        $response->assertDontSee('Action by SuperAdmin');
    }

    public function test_activity_logger_auto_resolves_user_id_from_account(): void
    {
        $account = Account::create([
            'user_id'      => $this->regularUser->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'name'         => 'Auto Resolve Test Account',
            'access_token' => 'dummy_token',
            'is_active'    => true,
        ]);

        // Log without passing userId explicitly
        $log = ActivityLogger::log(
            SystemActivityLog::EVENT_ACCOUNT_SYNC,
            'Background Webhook Sync',
            SystemActivityLog::STATUS_SUCCESS,
            null,
            null,
            120,
            $account->id,
            null // Explicitly null userId
        );

        $this->assertNotNull($log);
        $this->assertEquals($this->regularUser->id, $log->user_id);
    }

    public function test_queue_monitor_displays_failed_jobs(): void
    {
        // Insert dummy failed job
        DB::table('failed_jobs')->insert([
            'uuid'       => 'test-uuid-12345',
            'connection' => 'redis',
            'queue'      => 'default',
            'payload'    => json_encode([
                'displayName' => 'App\\Jobs\\ProcessCommentJob',
                'job'         => 'Illuminate\\Queue\\CallQueuedHandler@call',
                'data'        => ['comment_id' => 999],
            ]),
            'exception'  => "Exception: Connection timed out\nat /app/Jobs/ProcessCommentJob.php:45",
            'failed_at'  => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.system-activity.index', ['tab' => 'queue']));

        $response->assertStatus(200);
        $response->assertSee('ProcessCommentJob');
        $response->assertSee('Connection timed out');
    }

    public function test_admin_can_flush_all_failed_jobs(): void
    {
        DB::table('failed_jobs')->insert([
            'uuid'       => 'test-uuid-flush',
            'connection' => 'redis',
            'queue'      => 'default',
            'payload'    => json_encode(['displayName' => 'App\\Jobs\\TestJob']),
            'exception'  => 'Test Exception',
            'failed_at'  => now(),
        ]);

        $this->assertEquals(1, DB::table('failed_jobs')->count());

        $response = $this->actingAs($this->admin)->post(route('admin.system-activity.clear-failed'));

        $response->assertRedirect(route('admin.system-activity.index', ['tab' => 'queue']));
        $this->assertEquals(0, DB::table('failed_jobs')->count());
    }

    public function test_admin_can_prune_activity_logs(): void
    {
        // 1 old log (40 days old)
        DB::table('system_activity_logs')->insert([
            'user_id'    => $this->admin->id,
            'event_type' => SystemActivityLog::EVENT_ACCOUNT_SYNC,
            'action'     => 'Old log',
            'status'     => SystemActivityLog::STATUS_SUCCESS,
            'created_at' => now()->subDays(40),
            'updated_at' => now()->subDays(40),
        ]);

        // 1 recent log (2 days old)
        DB::table('system_activity_logs')->insert([
            'user_id'    => $this->admin->id,
            'event_type' => SystemActivityLog::EVENT_ACCOUNT_SYNC,
            'action'     => 'Recent log',
            'status'     => SystemActivityLog::STATUS_SUCCESS,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $this->assertEquals(2, SystemActivityLog::count());

        $response = $this->actingAs($this->admin)->post(route('admin.system-activity.clear-logs'), [
            'days' => 30,
        ]);

        $response->assertRedirect(route('admin.system-activity.index', ['tab' => 'logs']));
        $this->assertEquals(1, SystemActivityLog::count());
        $this->assertDatabaseHas('system_activity_logs', ['action' => 'Recent log']);
    }
}
