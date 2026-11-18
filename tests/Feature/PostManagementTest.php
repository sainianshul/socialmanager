<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_posts_index_renders_empty_state_when_no_posts_synced(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '123456789',
            'name' => 'Test Page',
            'access_token' => 'mock_token',
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.index'));

        $response->assertStatus(200);
        $response->assertSee('Posts');
        $response->assertSee('No Posts Found');
        $response->assertSee('Test Page');
    }

    public function test_posts_index_renders_synced_posts_in_gallery_mode(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '123456789',
            'name' => 'Public Policy Page',
            'access_token' => 'mock_token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => '123456789_987654321',
            'message' => 'Development project inaugurated today in our constituency.',
            'media_type' => 'photo',
            'media_url' => 'https://example.com/banner.jpg',
            'likes_count' => 142,
            'comments_count' => 38,
            'published_at' => now()->subHours(2),
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.index', ['view' => 'gallery']));

        $response->assertStatus(200);
        $response->assertSee('Development project inaugurated today');
        $response->assertSee('Public Policy Page');
        $response->assertSee('142'); // likes
        $response->assertSee('38');  // comments
    }

    public function test_posts_index_renders_synced_posts_in_table_mode(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '123456789',
            'name' => 'Health Care Initiative',
            'access_token' => 'mock_token',
        ]);

        Post::create([
            'account_id' => $account->id,
            'meta_post_id' => '123456789_111222333',
            'message' => 'Free medical camp organized this weekend.',
            'media_type' => 'photo',
            'likes_count' => 85,
            'comments_count' => 12,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.index', ['view' => 'table']));

        $response->assertStatus(200);
        $response->assertSee('Free medical camp organized this weekend');
        $response->assertSee('Health Care Initiative');
        $response->assertDontSee('ID: 123456789_111222333');
    }

    public function test_posts_filter_by_account(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $accountA = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '111',
            'name' => 'Account Alpha',
            'access_token' => 'token_a',
        ]);

        $accountB = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '222',
            'name' => 'Account Beta',
            'access_token' => 'token_b',
        ]);

        Post::create([
            'account_id' => $accountA->id,
            'meta_post_id' => 'post_a',
            'message' => 'Post from Alpha Page',
            'published_at' => now(),
        ]);

        Post::create([
            'account_id' => $accountB->id,
            'meta_post_id' => 'post_b',
            'message' => 'Post from Beta Page',
            'published_at' => now(),
        ]);

        // Filter for Account Alpha only
        $response = $this->actingAs($user)->get(route('admin.posts.index', ['account_id' => $accountA->id]));

        $response->assertStatus(200);
        $response->assertSee('Post from Alpha Page');
        $response->assertDontSee('Post from Beta Page');
    }

    public function test_posts_sync_fetches_and_persists_posts_from_meta(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '1404772002715952',
            'name' => 'Healthiiiii',
            'access_token' => 'valid_page_token',
            'is_active' => true,
        ]);

        // Mock Meta Graph API /posts response
        Http::fake([
            'https://graph.facebook.com/v20.0/1404772002715952/posts*' => Http::response([
                'data' => [
                    [
                        'id' => '1404772002715952_101',
                        'message' => 'First announcement on healthcare modernization.',
                        'created_time' => '2026-10-29T10:00:00+0000',
                        'permalink_url' => 'https://facebook.com/permalink101',
                        'attachments' => [
                            'data' => [
                                [
                                    'type' => 'photo',
                                    'media' => [
                                        'image' => ['src' => 'https://example.com/photo101.jpg'],
                                    ],
                                ],
                            ],
                        ],
                        'likes' => ['summary' => ['total_count' => 45]],
                        'comments' => ['summary' => ['total_count' => 19]],
                        'shares' => ['count' => 5],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.posts.sync', [
            'account_id' => $account->id,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'account_id' => $account->id,
            'meta_post_id' => '1404772002715952_101',
            'message' => 'First announcement on healthcare modernization.',
            'media_type' => 'photo',
            'shares_count' => 5,
        ]);
    }

    public function test_instagram_posts_page_renders_only_instagram_posts(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_999',
            'name' => 'Official FB Page',
            'access_token' => 'dummy_fb_token',
        ]);

        $igAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_acc_888',
            'name' => 'Design Studio IG',
            'username' => 'designstudio',
            'access_token' => 'dummy_ig_token',
        ]);

        $fbPost = Post::create([
            'account_id' => $fbAccount->id,
            'meta_post_id' => 'fb_post_1',
            'message' => 'Exclusive announcement on Facebook only',
            'published_at' => now()->subDay(),
        ]);

        $igPost = Post::create([
            'account_id' => $igAccount->id,
            'meta_post_id' => 'ig_post_1',
            'message' => 'Behind the scenes reel on Instagram',
            'media_type' => 'reel',
            'likes_count' => 350,
            'comments_count' => 45,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.instagram'));

        $response->assertStatus(200);
        $response->assertSee('Instagram Posts');
        $response->assertSee('Behind the scenes reel on Instagram');
        $response->assertSee('Design Studio IG');
        $response->assertSee('350');
        $response->assertDontSee('Exclusive announcement on Facebook only');
    }

    public function test_facebook_posts_page_renders_only_facebook_posts(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_777',
            'name' => 'Public Policy Bureau',
            'access_token' => 'dummy_fb_token',
        ]);

        $igAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_acc_666',
            'name' => 'Fashion Line IG',
            'access_token' => 'dummy_ig_token',
        ]);

        $fbPost = Post::create([
            'account_id' => $fbAccount->id,
            'meta_post_id' => 'fb_post_official_7',
            'message' => 'New infrastructure initiative launched for community welfare.',
            'media_type' => 'status',
            'likes_count' => 520,
            'comments_count' => 64,
            'shares_count' => 28,
            'published_at' => now(),
        ]);

        $igPost = Post::create([
            'account_id' => $igAccount->id,
            'meta_post_id' => 'ig_post_fashion_6',
            'message' => 'Summer collection photoshoot backstage',
            'media_type' => 'photo',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.facebook'));

        $response->assertStatus(200);
        $response->assertSee('Facebook Posts');
        $response->assertSee('New infrastructure initiative launched for community welfare.');
        $response->assertSee('Public Policy Bureau');
        $response->assertSee('520');
        $response->assertSee('28'); // shares
        $response->assertDontSee('Summer collection photoshoot backstage');
    }

    public function test_youtube_videos_page_renders_only_youtube_videos(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'yt_official_channel',
            'name' => 'Tech Masterclass',
            'subscriber_count' => 84000,
            'access_token' => 'dummy_yt_token',
        ]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_unrelated_123',
            'name' => 'Unrelated FB Page',
            'access_token' => 'dummy_fb_token',
        ]);

        $ytVideo = Post::create([
            'account_id' => $ytAccount->id,
            'platform_post_id' => 'yt_vid_abc123',
            'title' => 'Complete Docker & Kubernetes Tutorial 2026',
            'duration' => 745, // 12:25
            'views_count' => 18500, // 18.5K
            'likes_count' => 620,
            'comments_count' => 95,
            'privacy_status' => 'public',
            'published_at' => now(),
        ]);

        $fbPost = Post::create([
            'account_id' => $fbAccount->id,
            'meta_post_id' => 'fb_post_404',
            'message' => 'Status update strictly meant for Facebook timeline',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.youtube'));

        $response->assertStatus(200);
        $response->assertSee('YouTube Videos');
        $response->assertSee('Complete Docker & Kubernetes Tutorial 2026');
        $response->assertSee('Tech Masterclass');
        $response->assertSee('12:25');
        $response->assertSee('18.5K');
        $response->assertSee('620');
        $response->assertDontSee('Status update strictly meant for Facebook timeline');
    }

    public function test_sync_youtube_videos_artisan_command_success(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_civic_tech_channel',
            'name' => 'Civic Tech Channel',
            'access_token' => 'mock_google_token',
            'is_active' => true,
        ]);

        Http::fake([
            'https://www.googleapis.com/youtube/v3/channels*' => Http::response([
                'items' => [
                    [
                        'id' => 'UC_civic_tech_channel',
                        'contentDetails' => [
                            'relatedPlaylists' => [
                                'uploads' => 'UU_civic_tech_channel',
                            ],
                        ],
                        'statistics' => [
                            'subscriberCount' => '95000',
                            'videoCount' => '42',
                        ],
                    ],
                ],
            ], 200),

            'https://www.googleapis.com/youtube/v3/playlistItems*' => Http::response([
                'items' => [
                    [
                        'contentDetails' => [
                            'videoId' => 'vid_civic_999',
                        ],
                        'snippet' => [
                            'title' => 'AI for Modern Governance',
                        ],
                    ],
                ],
            ], 200),

            'https://www.googleapis.com/youtube/v3/videos*' => Http::response([
                'items' => [
                    [
                        'id' => 'vid_civic_999',
                        'snippet' => [
                            'title' => 'AI for Modern Governance',
                            'description' => 'A comprehensive review of AI applications in local council services.',
                            'publishedAt' => '2026-11-01T10:00:00Z',
                            'thumbnails' => [
                                'high' => ['url' => 'https://i.ytimg.com/vi/vid_civic_999/hqdefault.jpg'],
                            ],
                            'tags' => ['ai', 'governance', 'technology'],
                        ],
                        'contentDetails' => [
                            'duration' => 'PT18M45S', // 18m 45s = 1125s
                        ],
                        'statistics' => [
                            'viewCount' => '25400',
                            'likeCount' => '1200',
                            'commentCount' => '150',
                        ],
                        'status' => [
                            'privacyStatus' => 'public',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->artisan('sync:youtube-videos', ['--account_id' => $ytAccount->id])
            ->expectsOutputToContain('Found 1 active YouTube channel(s) to synchronize.')
            ->expectsOutputToContain('✓ Successfully synced 1 video(s) for Civic Tech Channel.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('posts', [
            'account_id' => $ytAccount->id,
            'meta_post_id' => 'vid_civic_999',
            'platform_post_id' => 'vid_civic_999',
            'title' => 'AI for Modern Governance',
            'duration' => 1125,
            'views_count' => 25400,
            'likes_count' => 1200,
            'comments_count' => 150,
            'privacy_status' => 'public',
        ]);

        $this->assertEquals(95000, $ytAccount->fresh()->subscriber_count);
        $this->assertEquals(42, $ytAccount->fresh()->video_count);
    }

    public function test_post_controller_sync_handles_youtube_channel(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_post_sync_yt',
            'name' => 'Metro Gov News',
            'access_token' => 'mock_google_token',
            'is_active' => true,
        ]);

        Http::fake([
            'https://www.googleapis.com/youtube/v3/channels*' => Http::response([
                'items' => [
                    [
                        'id' => 'UC_post_sync_yt',
                        'contentDetails' => [
                            'relatedPlaylists' => [
                                'uploads' => 'UU_post_sync_yt',
                            ],
                        ],
                    ],
                ],
            ], 200),

            'https://www.googleapis.com/youtube/v3/playlistItems*' => Http::response([
                'items' => [
                    [
                        'contentDetails' => ['videoId' => 'vid_metro_777'],
                    ],
                ],
            ], 200),

            'https://www.googleapis.com/youtube/v3/videos*' => Http::response([
                'items' => [
                    [
                        'id' => 'vid_metro_777',
                        'snippet' => [
                            'title' => 'Weekly Metro Council Address',
                            'description' => 'Highlights from Tuesday public meeting.',
                            'publishedAt' => '2026-11-03T12:00:00Z',
                        ],
                        'contentDetails' => ['duration' => 'PT5M30S'],
                        'statistics' => ['viewCount' => '3200', 'likeCount' => '95', 'commentCount' => '12'],
                        'status' => ['privacyStatus' => 'public'],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.posts.sync'), [
            'account_id' => $ytAccount->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'account_id' => $ytAccount->id,
            'meta_post_id' => 'vid_metro_777',
            'title' => 'Weekly Metro Council Address',
            'views_count' => 3200,
        ]);
    }
}

