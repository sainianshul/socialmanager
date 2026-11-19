<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ScheduledPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SchedulePostManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_schedule_posts_calendar_view_renders_successfully(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '123456789',
            'name' => 'Public Policy Page',
            'access_token' => 'mock_token',
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.schedule'));

        $response->assertStatus(200);
        $response->assertSee('Content Calendar');
        $response->assertSee('Public Policy Page');
        $response->assertSee('Calendar View');
        $response->assertSee('Upcoming List');
    }

    public function test_schedule_posts_events_api_returns_json_for_fullcalendar(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '123456789',
            'name' => 'Public Policy Page',
            'access_token' => 'mock_token',
        ]);

        $scheduledPost = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'post_type' => ScheduledPost::TYPE_PHOTO,
            'content' => 'Monthly health update for citizens.',
            'scheduled_at' => now()->addDays(2),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.posts.schedule.events', [
            'start' => now()->subDay()->toIso8601String(),
            'end' => now()->addDays(5)->toIso8601String(),
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => (string) $scheduledPost->id,
            'title' => 'Monthly health update for ci...',
        ]);
    }

    public function test_schedule_posts_store_creates_scheduled_post(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '123456789',
            'name' => 'Public Policy Page',
            'access_token' => 'mock_token',
        ]);

        $scheduledTime = now()->addDays(3)->format('Y-m-d\TH:i');

        $response = $this->actingAs($user)->post(route('admin.posts.schedule.store'), [
            'account_id' => $account->id,
            'post_type' => 'text',
            'content' => 'Announcement: Free drinking water supply project inaugurated.',
            'publish_mode' => 'schedule',
            'scheduled_at' => $scheduledTime,
        ]);

        $response->assertRedirect(route('admin.posts.schedule'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('scheduled_posts', [
            'account_id' => $account->id,
            'post_type' => 'text',
            'content' => 'Announcement: Free drinking water supply project inaugurated.',
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);
    }

    public function test_schedule_posts_publish_now_calls_meta_publishing_service(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '1404772002715952',
            'name' => 'Healthiiiii',
            'access_token' => 'mock_token',
            'is_active' => true,
        ]);

        $scheduledPost = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'post_type' => 'text',
            'content' => 'Live breaking announcement!',
            'scheduled_at' => now()->addDays(2),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/1404772002715952/feed' => Http::response([
                'id' => 'meta_post_live_9999',
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.posts.schedule.publish-now', $scheduledPost->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(ScheduledPost::STATUS_PUBLISHED, $scheduledPost->fresh()->status);
        $this->assertEquals('meta_post_live_9999', $scheduledPost->fresh()->meta_post_id);

        $this->assertDatabaseHas('posts', [
            'account_id' => $account->id,
            'meta_post_id' => 'meta_post_live_9999',
            'message' => 'Live breaking announcement!',
        ]);
    }

    public function test_publish_scheduled_posts_artisan_command(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '1404772002715952',
            'name' => 'Healthiiiii',
            'access_token' => 'mock_token',
            'is_active' => true,
        ]);

        $scheduledPost = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'post_type' => 'text',
            'content' => 'Due post to be published via cron',
            'scheduled_at' => now()->subMinute(),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/1404772002715952/feed' => Http::response([
                'id' => 'cron_published_id_123',
            ], 200),
        ]);

        $this->artisan('posts:publish-scheduled')
            ->expectsOutputToContain('Found 1 scheduled post(s) due for publishing.')
            ->expectsOutputToContain('✓ Post #' . $scheduledPost->id . ' published successfully!')
            ->assertExitCode(0);

        $this->assertEquals(ScheduledPost::STATUS_PUBLISHED, $scheduledPost->fresh()->status);
        $this->assertEquals('cron_published_id_123', $scheduledPost->fresh()->meta_post_id);
    }

    public function test_schedule_composer_renders_multi_platform_options_and_fields(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_101',
            'name' => 'Official FB Page',
            'access_token' => 'mock_token',
        ]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_channel_101',
            'name' => 'Ministry Tech YouTube',
            'access_token' => 'mock_token',
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.schedule'));

        $response->assertStatus(200);
        $response->assertSee('Target Platform');
        $response->assertSee('Facebook');
        $response->assertSee('Instagram');
        $response->assertSee('YouTube');
        $response->assertSee('Video Title');
        $response->assertSee('Video Description');
        $response->assertSee('Privacy Status');
        $response->assertSee('Ministry Tech YouTube');
    }

    public function test_schedule_posts_events_api_returns_youtube_platform_details(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_channel_202',
            'name' => 'Gov Education YT',
            'access_token' => 'mock_token',
        ]);

        $scheduledPost = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $ytAccount->id,
            'platform' => Account::TYPE_YOUTUBE,
            'post_type' => 'video',
            'title' => 'Digital Literacy Workshop Ep 1',
            'description' => 'Comprehensive tutorial series for digital skills.',
            'scheduled_at' => now()->addDays(2),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.posts.schedule.events', [
            'start' => now()->subDay()->toIso8601String(),
            'end' => now()->addDays(5)->toIso8601String(),
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => (string) $scheduledPost->id,
            'title' => 'Digital Literacy Workshop Ep...',
            'backgroundColor' => '#e02424',
            'borderColor' => '#e02424',
        ]);
    }

    public function test_schedule_posts_store_youtube_video_validation_requires_title(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_channel_val_1',
            'name' => 'Validation YT',
            'access_token' => 'mock_token',
        ]);

        $response = $this->actingAs($user)->post(route('admin.posts.schedule.store'), [
            'account_id' => $ytAccount->id,
            'platform' => Account::TYPE_YOUTUBE,
            'title' => '', // Empty title
            'privacy_status' => 'public',
            'publish_mode' => 'draft',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_schedule_posts_store_youtube_video_success(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_channel_store_2',
            'name' => 'City Council YT',
            'access_token' => 'mock_token',
        ]);

        $videoFile = UploadedFile::fake()->create('budget_report.mp4', 500, 'video/mp4');
        $thumbnailFile = UploadedFile::fake()->image('thumb.jpg', 1280, 720);
        $scheduledTime = now()->addDays(4)->format('Y-m-d\TH:i');

        $response = $this->actingAs($user)->post(route('admin.posts.schedule.store'), [
            'account_id' => $ytAccount->id,
            'platform' => Account::TYPE_YOUTUBE,
            'title' => 'Annual Budget Presentation 2026',
            'description' => 'Detailed financial allocation across education and infrastructure.',
            'tags' => 'budget, council, city, education',
            'privacy_status' => 'unlisted',
            'video_file' => $videoFile,
            'thumbnail' => $thumbnailFile,
            'publish_mode' => 'schedule',
            'scheduled_at' => $scheduledTime,
        ]);

        $response->assertRedirect(route('admin.posts.schedule'));
        $response->assertSessionHas('success');

        $scheduledPost = ScheduledPost::where('account_id', $ytAccount->id)->first();
        $this->assertNotNull($scheduledPost);
        $this->assertEquals(Account::TYPE_YOUTUBE, $scheduledPost->platform);
        $this->assertEquals(ScheduledPost::TYPE_VIDEO, $scheduledPost->post_type);
        $this->assertEquals('Annual Budget Presentation 2026', $scheduledPost->title);
        $this->assertEquals('unlisted', $scheduledPost->privacy_status);
        $this->assertContains('budget', $scheduledPost->tags);
        $this->assertContains('council', $scheduledPost->tags);
        $this->assertNotNull($scheduledPost->thumbnail_path);
        $this->assertCount(1, $scheduledPost->media_urls);
    }

    public function test_schedule_posts_store_instagram_post_requires_media(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $igAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_act_99',
            'name' => 'City Tourism IG',
            'access_token' => 'mock_token',
        ]);

        $response = $this->actingAs($user)->post(route('admin.posts.schedule.store'), [
            'account_id' => $igAccount->id,
            'platform' => Account::TYPE_INSTAGRAM,
            'post_type' => 'photo',
            'content' => 'Beautiful sunset at the city marina!',
            'publish_mode' => 'schedule',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('media_files');
    }

    public function test_schedule_posts_store_instagram_post_success(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $igAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_act_99',
            'name' => 'City Tourism IG',
            'access_token' => 'mock_token',
        ]);

        $imageFile = UploadedFile::fake()->image('marina.jpg', 1080, 1080);

        $response = $this->actingAs($user)->post(route('admin.posts.schedule.store'), [
            'account_id' => $igAccount->id,
            'platform' => Account::TYPE_INSTAGRAM,
            'post_type' => 'photo',
            'content' => 'Visit our newly renovated city marina this weekend! #tourism',
            'media_files' => [$imageFile],
            'publish_mode' => 'schedule',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('admin.posts.schedule'));
        $response->assertSessionHas('success');

        $scheduledPost = ScheduledPost::where('account_id', $igAccount->id)->first();
        $this->assertNotNull($scheduledPost);
        $this->assertEquals(Account::TYPE_INSTAGRAM, $scheduledPost->platform);
        $this->assertEquals(ScheduledPost::TYPE_PHOTO, $scheduledPost->post_type);
        $this->assertCount(1, $scheduledPost->media_urls);
    }

    public function test_schedule_posts_publish_now_calls_youtube_publishing_service(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_channel_live_33',
            'name' => 'Metro Press YT',
            'access_token' => 'mock_token',
            'is_active' => true,
        ]);

        $scheduledPost = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $ytAccount->id,
            'platform' => Account::TYPE_YOUTUBE,
            'post_type' => 'video',
            'title' => 'Live Press Briefing',
            'description' => 'Important announcements from Mayor office.',
            'privacy_status' => 'public',
            'scheduled_at' => now()->addDays(1),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        Http::fake([
            'https://www.googleapis.com/upload/youtube/v3/videos*' => Http::response([
                'id' => 'yt_vid_uploaded_8888',
                'snippet' => [
                    'title' => 'Live Press Briefing',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.posts.schedule.publish-now', $scheduledPost->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(ScheduledPost::STATUS_PUBLISHED, $scheduledPost->fresh()->status);
        $this->assertEquals('yt_vid_uploaded_8888', $scheduledPost->fresh()->meta_post_id);

        $this->assertDatabaseHas('posts', [
            'account_id' => $ytAccount->id,
            'meta_post_id' => 'yt_vid_uploaded_8888',
            'title' => 'Live Press Briefing',
            'media_type' => 'video',
        ]);
    }

    public function test_user_cannot_schedule_post_for_another_users_account(): void
    {
        $userA = User::factory()->create(['role' => User::ROLE_USER]);
        $userB = User::factory()->create(['role' => User::ROLE_USER]);

        $accountB = Account::create([
            'user_id' => $userB->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_user_b',
            'name' => 'User B Private Page',
            'access_token' => 'mock_token',
        ]);

        $response = $this->actingAs($userA)->post(route('admin.posts.schedule.store'), [
            'account_id' => $accountB->id,
            'platform' => Account::TYPE_FACEBOOK,
            'post_type' => 'text',
            'content' => 'Hacking another user channel',
            'publish_mode' => 'draft',
        ]);

        $response->assertStatus(403);
    }

    public function test_calendar_view_renders_platform_filter_tabs_and_legend(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_cal_1',
            'name' => 'Calendar Test Page',
            'access_token' => 'mock_token',
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.schedule'));

        $response->assertStatus(200);
        $response->assertSee('All Platforms');
        $response->assertSee('Facebook');
        $response->assertSee('Instagram');
        $response->assertSee('YouTube');
        $response->assertSee('Monthly Publishing Schedule');
        $response->assertSee('FullCalendar');
    }

    public function test_calendar_events_api_filters_by_platform(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_cal_2',
            'name' => 'FB Calendar Page',
            'access_token' => 'mock_token',
        ]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_cal_yt_2',
            'name' => 'YT Calendar Channel',
            'access_token' => 'mock_token',
        ]);

        $fbPost = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $fbAccount->id,
            'platform' => Account::TYPE_FACEBOOK,
            'post_type' => 'text',
            'content' => 'FB Post Content 123',
            'scheduled_at' => now()->addDays(2),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        $ytPost = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $ytAccount->id,
            'platform' => Account::TYPE_YOUTUBE,
            'post_type' => 'video',
            'title' => 'YT Video Title 456',
            'description' => 'YT Video Description',
            'scheduled_at' => now()->addDays(2),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        // Request events with platform=3 (YouTube only)
        $response = $this->actingAs($user)->getJson(route('admin.posts.schedule.events', [
            'platform' => Account::TYPE_YOUTUBE,
            'start' => now()->subDay()->toIso8601String(),
            'end' => now()->addDays(5)->toIso8601String(),
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => (string) $ytPost->id]);
        $response->assertJsonMissing(['id' => (string) $fbPost->id]);
    }

    public function test_calendar_show_endpoint_returns_youtube_metadata(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_show_yt_3',
            'name' => 'Press Release YT',
            'access_token' => 'mock_token',
        ]);

        $ytPost = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $ytAccount->id,
            'platform' => Account::TYPE_YOUTUBE,
            'post_type' => 'video',
            'title' => 'Press Conference 2026',
            'description' => 'Annual address to the press corps.',
            'tags' => ['conference', 'news'],
            'privacy_status' => 'unlisted',
            'scheduled_at' => now()->addDays(1),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.posts.schedule.show', $ytPost->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $ytPost->id,
            'title' => 'Press Conference 2026',
            'privacy_status' => 'unlisted',
            'platform' => Account::TYPE_YOUTUBE,
        ]);
    }

    public function test_edit_scheduled_post_renders_for_owner_with_locked_platform(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_edit_123',
            'name' => 'Citizen Connect IG',
            'access_token' => 'mock_token',
        ]);

        $post = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'platform' => Account::TYPE_INSTAGRAM,
            'post_type' => ScheduledPost::TYPE_PHOTO,
            'content' => 'Old caption before edit',
            'media_urls' => ['uploads/scheduled_posts/old_pic.jpg'],
            'scheduled_at' => now()->addDays(2),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.schedule.edit', $post->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Scheduled Post #' . $post->id);
        $response->assertSee('Citizen Connect IG');
        $response->assertSee('Old caption before edit');
        $response->assertSee('Platform Locked');
    }

    public function test_edit_scheduled_post_blocks_already_published_post(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_published_123',
            'name' => 'Published FB Page',
            'access_token' => 'mock_token',
        ]);

        $post = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'platform' => Account::TYPE_FACEBOOK,
            'post_type' => ScheduledPost::TYPE_TEXT,
            'content' => 'Already published live post',
            'scheduled_at' => now()->subDay(),
            'status' => ScheduledPost::STATUS_PUBLISHED,
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.schedule.edit', $post->id));

        $response->assertRedirect(route('admin.posts.schedule'));
        $response->assertSessionHas('error', 'Published or currently publishing posts cannot be edited.');
    }

    public function test_update_scheduled_facebook_post_updates_content_and_time(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_upd_123',
            'name' => 'Ward Development Page',
            'access_token' => 'mock_token',
        ]);

        $post = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'platform' => Account::TYPE_FACEBOOK,
            'post_type' => ScheduledPost::TYPE_TEXT,
            'content' => 'Old draft announcement',
            'scheduled_at' => now()->addDays(1),
            'status' => ScheduledPost::STATUS_DRAFT,
        ]);

        $newDate = now()->addDays(3)->format('Y-m-d\TH:i');

        $response = $this->actingAs($user)->put(route('admin.posts.schedule.update', $post->id), [
            'post_type' => 'text',
            'content' => 'Updated official ward development announcement!',
            'publish_mode' => 'schedule',
            'scheduled_at' => $newDate,
        ]);

        $response->assertRedirect(route('admin.posts.schedule'));
        $response->assertSessionHas('success');

        $post->refresh();
        $this->assertEquals('Updated official ward development announcement!', $post->content);
        $this->assertEquals(ScheduledPost::STATUS_SCHEDULED, $post->status);
    }

    public function test_update_scheduled_instagram_post_replaces_media(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_media_upd',
            'name' => 'Ward Photos IG',
            'access_token' => 'mock_token',
        ]);

        // Existing media
        $oldFile = UploadedFile::fake()->image('old_photo.jpg');
        $oldPath = $oldFile->storeAs('uploads/scheduled_posts', 'old_photo.jpg', 'public');

        $post = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'platform' => Account::TYPE_INSTAGRAM,
            'post_type' => ScheduledPost::TYPE_PHOTO,
            'content' => 'Old caption',
            'media_urls' => [$oldPath],
            'scheduled_at' => now()->addDays(2),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        // Upload replacement photo
        $newPhoto = UploadedFile::fake()->image('new_banner.png');

        $response = $this->actingAs($user)->put(route('admin.posts.schedule.update', $post->id), [
            'post_type' => 'photo',
            'content' => 'New refreshed banner caption',
            'media_files' => [$newPhoto],
            'publish_mode' => 'schedule',
            'scheduled_at' => now()->addDays(4)->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('admin.posts.schedule'));
        $post->refresh();

        $this->assertEquals('New refreshed banner caption', $post->content);
        $this->assertCount(1, $post->media_urls);
        $this->assertNotEquals($oldPath, $post->media_urls[0]);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($post->media_urls[0]);
    }

    public function test_update_scheduled_youtube_post_updates_title_tags_and_privacy(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_edit_yt_channel',
            'name' => 'Ward Press YT',
            'access_token' => 'mock_token',
        ]);

        $post = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'platform' => Account::TYPE_YOUTUBE,
            'post_type' => ScheduledPost::TYPE_VIDEO,
            'title' => 'Original Draft Video Title',
            'description' => 'Original description',
            'tags' => ['old_tag'],
            'privacy_status' => 'private',
            'media_urls' => ['uploads/youtube_videos/sample_video.mp4'],
            'scheduled_at' => now()->addDays(2),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        $response = $this->actingAs($user)->put(route('admin.posts.schedule.update', $post->id), [
            'title' => 'Updated Finalized Video Title',
            'description' => 'Updated comprehensive description for citizens',
            'tags' => 'development, water, roads',
            'privacy_status' => 'public',
            'publish_mode' => 'schedule',
            'scheduled_at' => now()->addDays(5)->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('admin.posts.schedule'));
        $post->refresh();

        $this->assertEquals('Updated Finalized Video Title', $post->title);
        $this->assertEquals('Updated comprehensive description for citizens', $post->description);
        $this->assertEquals(['development', 'water', 'roads'], $post->tags);
        $this->assertEquals('public', $post->privacy_status);
        $this->assertEquals(ScheduledPost::STATUS_SCHEDULED, $post->status);
    }

    public function test_delete_scheduled_post_cancels_post_and_removes_media(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_del_test',
            'name' => 'To Be Deleted Page',
            'access_token' => 'mock_token',
        ]);

        $file = UploadedFile::fake()->image('del_img.jpg');
        $path = $file->storeAs('uploads/scheduled_posts', 'del_img.jpg', 'public');

        $post = ScheduledPost::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'platform' => Account::TYPE_FACEBOOK,
            'post_type' => ScheduledPost::TYPE_PHOTO,
            'content' => 'Post to cancel',
            'media_urls' => [$path],
            'scheduled_at' => now()->addDays(2),
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        $response = $this->actingAs($user)->delete(route('admin.posts.schedule.destroy', $post->id));

        $response->assertRedirect(route('admin.posts.schedule'));
        $this->assertSoftDeleted('scheduled_posts', ['id' => $post->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_schedule_posts_store_multi_account_creates_multiple_scheduled_posts(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbPage1 = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_multi_1',
            'name' => 'Main Brand Page',
            'access_token' => 'mock_token',
        ]);

        $fbPage2 = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_multi_2',
            'name' => 'Secondary Brand Page',
            'access_token' => 'mock_token',
        ]);

        $scheduledTime = now()->addDays(3)->format('Y-m-d\TH:i');

        $response = $this->actingAs($user)->post(route('admin.posts.schedule.store'), [
            'account_ids' => [$fbPage1->id, $fbPage2->id],
            'post_type' => 'text',
            'content' => 'Multi-channel broadcast announcement across both pages.',
            'publish_mode' => 'schedule',
            'scheduled_at' => $scheduledTime,
        ]);

        $response->assertRedirect(route('admin.posts.schedule'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('scheduled_posts', [
            'account_id' => $fbPage1->id,
            'content' => 'Multi-channel broadcast announcement across both pages.',
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);

        $this->assertDatabaseHas('scheduled_posts', [
            'account_id' => $fbPage2->id,
            'content' => 'Multi-channel broadcast announcement across both pages.',
            'status' => ScheduledPost::STATUS_SCHEDULED,
        ]);
    }

    public function test_schedule_posts_store_multi_account_publish_now_publishes_all(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbPage = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_live_multi_1',
            'name' => 'Live Page 1',
            'access_token' => 'mock_token',
        ]);

        $igPage = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_live_multi_2',
            'name' => 'Live IG Account',
            'access_token' => 'mock_token',
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/fb_live_multi_1/photos' => Http::response(['id' => 'fb_published_multi_1'], 200),
            'https://graph.facebook.com/v20.0/ig_live_multi_2/media' => Http::response(['id' => 'creation_id_123'], 200),
            'https://graph.facebook.com/v20.0/ig_live_multi_2/media_publish' => Http::response(['id' => 'ig_published_multi_2'], 200),
        ]);

        Storage::fake('public');
        $imageFile = UploadedFile::fake()->image('broadcast.jpg', 800, 800);

        $response = $this->actingAs($user)->post(route('admin.posts.schedule.store'), [
            'account_ids' => [$fbPage->id, $igPage->id],
            'post_type' => 'photo',
            'content' => 'Live multi-channel image post to FB & IG!',
            'media_files' => [$imageFile],
            'publish_mode' => 'now',
        ]);

        $response->assertRedirect(route('admin.posts.schedule'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('scheduled_posts', [
            'account_id' => $fbPage->id,
            'status' => ScheduledPost::STATUS_PUBLISHED,
        ]);

        $this->assertDatabaseHas('scheduled_posts', [
            'account_id' => $igPage->id,
            'status' => ScheduledPost::STATUS_PUBLISHED,
        ]);
    }
}
