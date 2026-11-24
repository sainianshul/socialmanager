<?php

namespace Tests\Feature;

use App\Jobs\ProcessCommentJob;
use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CommentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_comments_index_renders_empty_state_when_no_comments_synced(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '123456789',
            'name' => 'Test Page',
            'access_token' => 'mock_token',
        ]);

        $response = $this->actingAs($user)->get(route('admin.comments.index'));

        $response->assertStatus(200);
        $response->assertSee('Comments');
        $response->assertSee('No Comments Found');
        $response->assertSee('Test Page');
    }

    public function test_comments_index_renders_comment_with_parent_post_context(): void
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
            'meta_post_id' => 'post_1001',
            'message' => 'Free eye checkup camp organized in Ward 12.',
            'media_type' => 'photo',
            'published_at' => now()->subDay(),
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'comment_999',
            'commenter_name' => 'Vikas Yadav',
            'message' => 'Bahut hi sarahaniya prayas sir ji!',
            'sentiment' => 'positive',
            'status' => Comment::STATUS_PENDING_REVIEW,
            'ai_reply' => 'Dhanyavaad Vikas ji!',
            'commented_at' => now()->subHours(2),
        ]);

        $pageResponse = $this->actingAs($user)->get(route('admin.comments.index'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Comments');

        $dataResponse = $this->actingAs($user)->getJson(route('admin.comments.data'));
        $dataResponse->assertStatus(200);
        $content = $dataResponse->getContent();
        $this->assertStringContainsString('Vikas Yadav', $content);
        $this->assertStringContainsString('Bahut hi sarahaniya prayas sir ji!', $content);
        $this->assertStringContainsString('Free eye checkup camp', $content);
        $this->assertStringContainsString('Pending Review', $content);
        $this->assertStringContainsString('Positive', $content);
    }

    public function test_long_comment_renders_expandable_more_toggle(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '123456789_long',
            'name' => 'Public Policy Page',
            'access_token' => 'mock_token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'post_1002',
            'message' => 'Post message',
        ]);

        $longMessage = 'Namaskar neta ji, hamare kshetra me pichle 2 mahine se sadak ka nirmaan karya adhoora pada hua hai aur pani ki pipe line bhi tooti hui hai jisse logon ko aane jaane me bahut pareshani ho rahi hai.';

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'comment_long_1',
            'commenter_name' => 'Rajesh Meena',
            'message' => $longMessage,
            'sentiment' => 'negative',
            'status' => Comment::STATUS_PENDING_REVIEW,
        ]);

        $dataResponse = $this->actingAs($user)->getJson(route('admin.comments.data'));
        $dataResponse->assertStatus(200);
        $messageHtml = $dataResponse->json('data.0.message');

        $this->assertStringContainsString('btn-comment-more', $messageHtml);
        $this->assertStringContainsString('btn-comment-less', $messageHtml);
        $this->assertStringContainsString('more</a>', $messageHtml);
        $this->assertStringContainsString('less</a>', $messageHtml);
        $this->assertStringContainsString('comment-text-short', $messageHtml);
        $this->assertStringContainsString('comment-text-full', $messageHtml);
    }

    public function test_comment_approve_action_marks_comment_as_replied(): void
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
            'meta_post_id' => 'post_1001',
            'message' => 'Post message',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'comment_888',
            'commenter_name' => 'Citizen Amit',
            'message' => 'Can you share venue details?',
            'status' => Comment::STATUS_PENDING_REVIEW,
            'ai_reply' => 'Yes, venue is City Community Center.',
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/comment_888/comments' => Http::response([
                'id' => 'reply_comment_999',
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.comments.approve', $comment->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(Comment::STATUS_REPLIED, $comment->fresh()->status);
        $this->assertNotNull($comment->fresh()->replied_at);
    }

    public function test_comment_toggle_hide_action(): void
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
            'meta_post_id' => 'post_1001',
            'message' => 'Post message',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'comment_777',
            'commenter_name' => 'Spammer',
            'message' => 'Spam link here',
            'status' => Comment::STATUS_PENDING_REVIEW,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/comment_777' => Http::response(['success' => true], 200),
        ]);

        // Hide
        $response = $this->actingAs($user)->post(route('admin.comments.toggle-hide', $comment->id));
        $response->assertRedirect();
        $this->assertEquals(Comment::STATUS_HIDDEN, $comment->fresh()->status);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'comment_777') && ($request['is_hidden'] ?? false) === true;
        });

        // Unhide
        $response2 = $this->actingAs($user)->post(route('admin.comments.toggle-hide', $comment->id));
        $response2->assertRedirect();
        $this->assertEquals(Comment::STATUS_PENDING_REVIEW, $comment->fresh()->status);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'comment_777') && ($request['is_hidden'] ?? null) === false;
        });
    }

    public function test_comments_sync_fetches_and_persists_comments_from_meta(): void
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

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => '1404772002715952_post_test',
            'message' => 'Healthy Habits Announcement',
            'published_at' => now(),
        ]);

        // Mock Meta Graph API comments endpoint
        Http::fake([
            'https://graph.facebook.com/v20.0/1404772002715952_post_test/comments*' => Http::response([
                'data' => [
                    [
                        'id' => '1404772002715952_post_test_comment_1',
                        'message' => 'Great initiative for our health!',
                        'created_time' => '2026-10-30T10:15:00+0000',
                        'from' => [
                            'name' => 'Pooja Verma',
                            'id' => 'fb_user_123',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.comments.sync', [
            'account_id' => $account->id,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => '1404772002715952_post_test_comment_1',
            'commenter_name' => 'Pooja Verma',
            'message' => 'Great initiative for our health!',
            'status' => Comment::STATUS_PENDING_REVIEW,
        ]);
    }

    public function test_manual_comment_publishes_to_facebook_post(): void
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

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => '1404772002715952_post_test',
            'message' => 'Important announcement',
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/1404772002715952_post_test/comments' => Http::response([
                'id' => '1404772002715952_post_test_manual_1',
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.comments.store-manual'), [
            'post_id' => $post->id,
            'message' => 'This is a manual announcement comment.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => '1404772002715952_post_test_manual_1',
            'message' => 'This is a manual announcement comment.',
            'status' => Comment::STATUS_REPLIED,
        ]);
    }

    public function test_reply_to_comment_publishes_to_meta(): void
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

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => '1404772002715952_post_test',
            'message' => 'Camp details',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'comment_to_reply_123',
            'commenter_name' => 'Citizen Ravi',
            'message' => 'What is the timing?',
            'status' => Comment::STATUS_PENDING_REVIEW,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/comment_to_reply_123/comments' => Http::response([
                'id' => 'reply_meta_comment_456',
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.comments.reply', $comment->id), [
            'reply_message' => 'Timing is 10:00 AM to 5:00 PM.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(Comment::STATUS_REPLIED, $comment->fresh()->status);
        $this->assertEquals('Timing is 10:00 AM to 5:00 PM.', $comment->fresh()->ai_reply);
        $this->assertEquals('reply_meta_comment_456', $comment->fresh()->meta_reply_id);

        // Verify parent comment is updated and channel's own reply is not inserted as separate row
        $this->assertDatabaseMissing('comments', [
            'parent_comment_id' => $comment->id,
            'meta_comment_id' => 'reply_meta_comment_456',
        ]);
    }

    public function test_instagram_comments_page_renders_only_instagram_comments_and_accounts(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $igAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_meta_page_100',
            'instagram_business_account_id' => 'ig_biz_100',
            'name' => 'Studio Instagram',
            'username' => 'studio_ig',
            'access_token' => 'ig_token',
            'is_active' => true,
        ]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_200',
            'name' => 'Political FB Page',
            'access_token' => 'fb_token',
            'is_active' => true,
        ]);

        $igPost = Post::create([
            'account_id' => $igAccount->id,
            'meta_post_id' => 'ig_post_1',
            'message' => 'Amazing sunset photoshoot reel #visuals',
        ]);

        $fbPost = Post::create([
            'account_id' => $fbAccount->id,
            'meta_post_id' => 'fb_post_2',
            'message' => 'Facebook political update on roads.',
        ]);

        $igComment = Comment::create([
            'post_id' => $igPost->id,
            'account_id' => $igAccount->id,
            'meta_comment_id' => 'ig_comm_111',
            'commenter_name' => 'art_lover_99',
            'message' => 'Love this reel composition!',
            'sentiment' => 'positive',
            'status' => Comment::STATUS_PENDING_REVIEW,
            'commented_at' => now(),
        ]);

        $fbComment = Comment::create([
            'post_id' => $fbPost->id,
            'account_id' => $fbAccount->id,
            'meta_comment_id' => 'fb_comm_222',
            'commenter_name' => 'fb_citizen_44',
            'message' => 'Fix the potholes in street 5 please.',
            'sentiment' => 'negative',
            'status' => Comment::STATUS_PENDING_REVIEW,
            'commented_at' => now(),
        ]);

        // 1. Check Instagram Comments HTML Page
        $pageResponse = $this->actingAs($user)->get(route('admin.comments.instagram'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Instagram Comments');
        $pageResponse->assertSee('btn-toggle-stats', false);
        $pageResponse->assertSee('analytics-stats-row', false);
        $pageResponse->assertSee('Studio Instagram');
        // The Facebook account should not appear in the accounts filter dropdown on the Instagram page
        $pageResponse->assertDontSee('<option value="' . $fbAccount->id . '">Political FB Page</option>', false);

        // 2. Check Instagram DataTable Data Endpoint
        $dataResponse = $this->actingAs($user)->getJson(route('admin.comments.instagram.data'));
        $dataResponse->assertStatus(200);
        $content = $dataResponse->getContent();

        $this->assertStringContainsString('art_lover_99', $content);
        $this->assertStringContainsString('Love this reel composition!', $content);
        // Facebook comments must NOT be included in Instagram data
        $this->assertStringNotContainsString('fb_citizen_44', $content);
        $this->assertStringNotContainsString('Fix the potholes in street 5', $content);
    }

    public function test_instagram_comments_data_filters_by_status_tab(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $igAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_meta_page_300',
            'name' => 'Fashion Studio',
            'access_token' => 'ig_token_300',
            'is_active' => true,
        ]);

        $post = Post::create([
            'account_id' => $igAccount->id,
            'meta_post_id' => 'ig_post_300',
            'message' => 'New summer arrival outfits',
        ]);

        $pendingComment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $igAccount->id,
            'meta_comment_id' => 'comm_pending_1',
            'commenter_name' => 'buyer_one',
            'message' => 'Can you share price details?',
            'status' => Comment::STATUS_PENDING_REVIEW,
            'commented_at' => now(),
        ]);

        $repliedComment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $igAccount->id,
            'meta_comment_id' => 'comm_replied_2',
            'commenter_name' => 'buyer_two',
            'message' => 'Received the package thanks!',
            'status' => Comment::STATUS_REPLIED,
            'ai_reply' => 'Glad to hear that!',
            'commented_at' => now()->subHour(),
        ]);

        // Filter by pending status tab
        $pendingResponse = $this->actingAs($user)->getJson(route('admin.comments.instagram.data', ['status' => 'pending']));
        $pendingResponse->assertStatus(200);
        $pendingContent = $pendingResponse->getContent();
        $this->assertStringContainsString('buyer_one', $pendingContent);
        $this->assertStringNotContainsString('buyer_two', $pendingContent);

        // Filter by replied status tab
        $repliedResponse = $this->actingAs($user)->getJson(route('admin.comments.instagram.data', ['status' => 'replied']));
        $repliedResponse->assertStatus(200);
        $repliedContent = $repliedResponse->getContent();
        $this->assertStringContainsString('buyer_two', $repliedContent);
        $this->assertStringNotContainsString('buyer_one', $repliedContent);
    }

    public function test_reply_to_instagram_comment_uses_replies_endpoint(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $igAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_meta_page_400',
            'name' => 'Design House',
            'access_token' => 'ig_token_400',
            'is_active' => true,
        ]);

        $post = Post::create([
            'account_id' => $igAccount->id,
            'meta_post_id' => 'ig_post_400',
            'message' => 'Design showcase',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $igAccount->id,
            'meta_comment_id' => 'ig_comm_target_555',
            'commenter_name' => 'insta_fan',
            'message' => 'Where can I order this?',
            'status' => Comment::STATUS_PENDING_REVIEW,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/ig_comm_target_555/replies' => Http::response([
                'id' => 'ig_reply_response_777',
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.comments.reply', $comment->id), [
            'reply_message' => 'Check the link in our bio for direct orders!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(Comment::STATUS_REPLIED, $comment->fresh()->status);
        $this->assertEquals('Check the link in our bio for direct orders!', $comment->fresh()->ai_reply);
        $this->assertEquals('ig_reply_response_777', $comment->fresh()->meta_reply_id);
    }

    public function test_facebook_comments_page_renders_only_facebook_comments_and_accounts(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_500',
            'name' => 'Official City Council Page',
            'access_token' => 'fb_token_500',
            'is_active' => true,
        ]);

        $igAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_meta_page_600',
            'name' => 'City Council IG',
            'access_token' => 'ig_token_600',
            'is_active' => true,
        ]);

        $fbPost = Post::create([
            'account_id' => $fbAccount->id,
            'meta_post_id' => 'fb_post_500',
            'message' => 'New community library opening this Saturday in Sector 4.',
        ]);

        $igPost = Post::create([
            'account_id' => $igAccount->id,
            'meta_post_id' => 'ig_post_600',
            'message' => 'Library photo gallery on Instagram #books',
        ]);

        $fbComment = Comment::create([
            'post_id' => $fbPost->id,
            'account_id' => $fbAccount->id,
            'meta_comment_id' => 'fb_comm_555',
            'commenter_name' => 'Sunil Gupta',
            'message' => 'Great initiative for local students!',
            'sentiment' => 'positive',
            'status' => Comment::STATUS_PENDING_REVIEW,
            'commented_at' => now(),
        ]);

        $igComment = Comment::create([
            'post_id' => $igPost->id,
            'account_id' => $igAccount->id,
            'meta_comment_id' => 'ig_comm_666',
            'commenter_name' => 'bookworm_delhi',
            'message' => 'Love the aesthetic reading room vibe',
            'sentiment' => 'positive',
            'status' => Comment::STATUS_PENDING_REVIEW,
            'commented_at' => now(),
        ]);

        // 1. Check Facebook Comments HTML Page
        $pageResponse = $this->actingAs($user)->get(route('admin.comments.facebook'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Facebook Comments');
        $pageResponse->assertSee('btn-toggle-stats', false);
        $pageResponse->assertSee('analytics-stats-row', false);
        $pageResponse->assertSee('Official City Council Page');
        // Instagram account should NOT appear in the Facebook page select dropdown
        $pageResponse->assertDontSee('<option value="' . $igAccount->id . '">City Council IG</option>', false);

        // 2. Check Facebook DataTable Data Endpoint
        $dataResponse = $this->actingAs($user)->getJson(route('admin.comments.facebook.data'));
        $dataResponse->assertStatus(200);
        $content = $dataResponse->getContent();

        $this->assertStringContainsString('Sunil Gupta', $content);
        $this->assertStringContainsString('Great initiative for local students!', $content);
        // Instagram comments must NOT be included in Facebook data
        $this->assertStringNotContainsString('bookworm_delhi', $content);
        $this->assertStringNotContainsString('Love the aesthetic reading room', $content);
    }

    public function test_facebook_comments_data_filters_by_status_tab(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_700',
            'name' => 'Civic Forum',
            'access_token' => 'fb_token_700',
            'is_active' => true,
        ]);

        $post = Post::create([
            'account_id' => $fbAccount->id,
            'meta_post_id' => 'fb_post_700',
            'message' => 'Public consultation on park renovation',
        ]);

        $pendingComment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $fbAccount->id,
            'meta_comment_id' => 'fb_pending_1',
            'commenter_name' => 'Resident Amit',
            'message' => 'Will there be a jogging track?',
            'status' => Comment::STATUS_PENDING_REVIEW,
            'commented_at' => now(),
        ]);

        $repliedComment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $fbAccount->id,
            'meta_comment_id' => 'fb_replied_2',
            'commenter_name' => 'Resident Neha',
            'message' => 'Thank you for adding outdoor gym equipment!',
            'status' => Comment::STATUS_REPLIED,
            'ai_reply' => 'You are welcome Neha!',
            'commented_at' => now()->subHour(),
        ]);

        // Filter by pending status tab
        $pendingResponse = $this->actingAs($user)->getJson(route('admin.comments.facebook.data', ['status' => 'pending']));
        $pendingResponse->assertStatus(200);
        $pendingContent = $pendingResponse->getContent();
        $this->assertStringContainsString('Resident Amit', $pendingContent);
        $this->assertStringNotContainsString('Resident Neha', $pendingContent);

        // Filter by replied status tab
        $repliedResponse = $this->actingAs($user)->getJson(route('admin.comments.facebook.data', ['status' => 'replied']));
        $repliedResponse->assertStatus(200);
        $repliedContent = $repliedResponse->getContent();
        $this->assertStringContainsString('Resident Neha', $repliedContent);
        $this->assertStringNotContainsString('Resident Amit', $repliedContent);
    }

    public function test_reply_to_facebook_comment_uses_comments_endpoint(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_800',
            'name' => 'Mayor Office',
            'access_token' => 'fb_token_800',
            'is_active' => true,
        ]);

        $post = Post::create([
            'account_id' => $fbAccount->id,
            'meta_post_id' => 'fb_post_800',
            'message' => 'Clean city drive',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $fbAccount->id,
            'meta_comment_id' => 'fb_comm_target_888',
            'commenter_name' => 'Citizen Rajesh',
            'message' => 'Where can we register for volunteer teams?',
            'status' => Comment::STATUS_PENDING_REVIEW,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/fb_comm_target_888/comments' => Http::response([
                'id' => 'fb_reply_response_999',
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.comments.reply', $comment->id), [
            'reply_message' => 'You can register at the municipal office counter or on our official website portal!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(Comment::STATUS_REPLIED, $comment->fresh()->status);
        $this->assertEquals('You can register at the municipal office counter or on our official website portal!', $comment->fresh()->ai_reply);
        $this->assertEquals('fb_reply_response_999', $comment->fresh()->meta_reply_id);
    }

    public function test_youtube_comments_page_renders_only_youtube_comments_and_accounts(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_tech_channel_101',
            'name' => 'Tech Insights Hindi',
            'access_token' => 'mock_google_token',
            'is_active' => true,
        ]);

        $fbAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_999',
            'name' => 'Social Facebook Page',
            'access_token' => 'fb_token_999',
            'is_active' => true,
        ]);

        $ytPost = Post::create([
            'account_id' => $ytAccount->id,
            'platform_post_id' => 'dQw4w9WgXcQ',
            'title' => 'Complete Laravel 12 Masterclass in Hindi',
            'published_at' => now(),
        ]);

        $fbPost = Post::create([
            'account_id' => $fbAccount->id,
            'meta_post_id' => 'fb_post_999',
            'message' => 'Facebook post announcement',
        ]);

        $ytComment = Comment::create([
            'post_id' => $ytPost->id,
            'account_id' => $ytAccount->id,
            'platform_comment_id' => 'yt_comm_1001',
            'commenter_name' => 'Aakash Dev',
            'message' => 'Sir please explain database indexing in next video!',
            'sentiment' => 'positive',
            'likes_count' => 14,
            'status' => Comment::STATUS_PENDING_REVIEW,
            'commented_at' => now(),
        ]);

        $fbComment = Comment::create([
            'post_id' => $fbPost->id,
            'account_id' => $fbAccount->id,
            'meta_comment_id' => 'fb_comm_999',
            'commenter_name' => 'FB User',
            'message' => 'Facebook political comment text',
            'status' => Comment::STATUS_PENDING_REVIEW,
            'commented_at' => now(),
        ]);

        // 1. Check YouTube Comments HTML Page
        $pageResponse = $this->actingAs($user)->get(route('admin.comments.youtube'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('YouTube Comments');
        $pageResponse->assertSee('btn-toggle-stats', false);
        $pageResponse->assertSee('analytics-stats-row', false);
        $pageResponse->assertSee('Tech Insights Hindi');
        // Facebook account should NOT appear in the YouTube channel filter dropdown
        $pageResponse->assertDontSee('<option value="' . $fbAccount->id . '">Social Facebook Page</option>', false);

        // 2. Check YouTube DataTable Data Endpoint
        $dataResponse = $this->actingAs($user)->getJson(route('admin.comments.youtube.data'));
        $dataResponse->assertStatus(200);
        $content = $dataResponse->getContent();

        $this->assertStringContainsString('Aakash Dev', $content);
        $this->assertStringContainsString('explain database indexing', $content);
        // Facebook comment must NOT be returned in YouTube data
        $this->assertStringNotContainsString('Facebook political comment text', $content);
    }

    public function test_youtube_comments_data_filters_by_status_tab(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $ytAccount = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_coding_channel',
            'name' => 'Coding Academy',
            'access_token' => 'mock_google_token',
            'is_active' => true,
        ]);

        $post = Post::create([
            'account_id' => $ytAccount->id,
            'platform_post_id' => 'video_12345',
            'title' => 'Vue 3 + Laravel Tutorial',
        ]);

        $pendingComment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $ytAccount->id,
            'platform_comment_id' => 'yt_pending_1',
            'commenter_name' => 'Coder Vikram',
            'message' => 'Where can I get the source code?',
            'status' => Comment::STATUS_PENDING_REVIEW,
            'commented_at' => now(),
        ]);

        $repliedComment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $ytAccount->id,
            'platform_comment_id' => 'yt_replied_2',
            'commenter_name' => 'Dev Sneha',
            'message' => 'Super clear tutorial, helped me build my project!',
            'status' => Comment::STATUS_REPLIED,
            'ai_reply' => 'Thanks Sneha! Best of luck with your project.',
            'commented_at' => now()->subHour(),
        ]);

        // Filter by pending status tab
        $pendingResponse = $this->actingAs($user)->getJson(route('admin.comments.youtube.data', ['status' => 'pending']));
        $pendingResponse->assertStatus(200);
        $pendingContent = $pendingResponse->getContent();
        $this->assertStringContainsString('Coder Vikram', $pendingContent);
        $this->assertStringNotContainsString('Dev Sneha', $pendingContent);

        // Filter by replied status tab
        $repliedResponse = $this->actingAs($user)->getJson(route('admin.comments.youtube.data', ['status' => 'replied']));
        $repliedResponse->assertStatus(200);
        $repliedContent = $repliedResponse->getContent();
        $this->assertStringContainsString('Dev Sneha', $repliedContent);
        $this->assertStringNotContainsString('Coder Vikram', $repliedContent);
    }

    public function test_process_comment_job_analyzes_sentiment_and_generates_ai_reply(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_ai_job_chan',
            'name' => 'Education Portal',
            'ai_tone' => 'formal',
            'ai_language' => 'English',
            'access_token' => 'mock_token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'platform_post_id' => 'vid_job_1',
            'title' => 'School Infrastructure Update',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'platform_comment_id' => 'c_job_pos_1',
            'commenter_name' => 'Citizen Anita',
            'message' => 'Awesome initiative! Great work done by the education team, thank you!',
            'status' => Comment::STATUS_PENDING_REVIEW,
        ]);

        $mockAi = \Mockery::mock(\App\Contracts\AIServiceInterface::class);
        $mockAi->shouldReceive('isConfigured')->andReturn(true);
        $mockAi->shouldReceive('analyzeAndDraftReply')->andReturn([
            'sentiment' => 'positive',
            'sentiment_score' => 0.85,
            'category' => 'appreciation',
            'is_abusive' => false,
            'abuse_confidence' => 0.05,
            'is_complaint' => false,
            'is_favor' => true,
            'is_against' => false,
            'ai_reply' => 'Thank you for your warm words Anita ji!',
        ]);
        $this->app->instance(\App\Contracts\AIServiceInterface::class, $mockAi);

        (new ProcessCommentJob($comment))->handle();

        $fresh = $comment->fresh();
        $this->assertEquals('positive', $fresh->sentiment);
        $this->assertEquals('appreciation', $fresh->category);
        $this->assertFalse((bool) $fresh->is_abusive);
        $this->assertTrue((bool) $fresh->is_favor);
        $this->assertEquals('Thank you for your warm words Anita ji!', $fresh->ai_reply);
    }

    public function test_process_comment_job_does_not_generate_fallback_dummy_reply_when_ai_is_unavailable(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_ai_job_chan_no_ai',
            'name' => 'Education Portal',
            'access_token' => 'mock_token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'platform_post_id' => 'vid_job_2',
            'title' => 'School Infrastructure Update',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'platform_comment_id' => 'c_job_pos_2',
            'commenter_name' => 'Citizen Anita',
            'message' => 'Awesome initiative! Great work done by the education team, thank you!',
            'status' => Comment::STATUS_PENDING_REVIEW,
        ]);

        // AI service returns null (simulating 403 or unconfigured state)
        $mockAi = \Mockery::mock(\App\Contracts\AIServiceInterface::class);
        $mockAi->shouldReceive('isConfigured')->andReturn(false);
        $this->app->instance(\App\Contracts\AIServiceInterface::class, $mockAi);

        (new ProcessCommentJob($comment))->handle();

        $fresh = $comment->fresh();
        $this->assertEquals('positive', $fresh->sentiment);
        $this->assertEquals('appreciation', $fresh->category);
        // Strictly no dummy/template reply generated
        $this->assertNull($fresh->ai_reply);
    }

    public function test_process_comment_job_auto_hides_abusive_troll_when_enabled(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_ai_troll_chan',
            'name' => 'Education Portal',
            'auto_hide_trolls' => true,
            'access_token' => 'mock_token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'platform_post_id' => 'vid_troll_1',
            'title' => 'City Council Budget',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'platform_comment_id' => 'c_troll_1',
            'commenter_name' => 'Anonymous Troll',
            'message' => 'This is a complete scam and fraud! All corrupt idiots!',
            'status' => Comment::STATUS_PENDING_REVIEW,
        ]);

        (new ProcessCommentJob($comment))->handle();

        $fresh = $comment->fresh();
        $this->assertEquals('negative', $fresh->sentiment);
        $this->assertEquals('troll', $fresh->category);
        $this->assertTrue((bool) $fresh->is_abusive);
        $this->assertTrue((bool) $fresh->should_hide);
        $this->assertEquals(Comment::STATUS_HIDDEN, $fresh->status);
        $this->assertNotNull($fresh->hidden_at);
    }

    public function test_sync_youtube_comments_artisan_command_success(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_cmd_sync_chan',
            'name' => 'Council Live',
            'access_token' => 'mock_google_token',
            'is_active' => true,
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'platform_post_id' => 'vid_cmd_101',
            'title' => 'City Council Live Stream',
        ]);

        Http::fake([
            'https://www.googleapis.com/youtube/v3/commentThreads*' => Http::response([
                'items' => [
                    [
                        'id' => 'yt_thread_cmd_888',
                        'snippet' => [
                            'videoId' => 'vid_cmd_101',
                            'topLevelComment' => [
                                'id' => 'yt_thread_cmd_888',
                                'snippet' => [
                                    'authorDisplayName' => 'Citizen John',
                                    'authorProfileImageUrl' => 'https://example.com/john.jpg',
                                    'textDisplay' => 'Will the recording be made available later?',
                                    'likeCount' => 7,
                                    'publishedAt' => '2026-11-04T09:00:00Z',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->artisan('sync:youtube-comments', ['--account_id' => $account->id])
            ->expectsOutputToContain('Found 1 active YouTube channel(s) to inspect for comment sync.')
            ->expectsOutputToContain('✓ Successfully synced 1 comment(s) for Council Live.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('comments', [
            'account_id' => $account->id,
            'post_id' => $post->id,
            'platform_comment_id' => 'yt_thread_cmd_888',
            'commenter_name' => 'Citizen John',
            'likes_count' => 7,
        ]);
    }

    public function test_comment_controller_sync_handles_youtube_comments(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_ctrl_sync_chan',
            'name' => 'Public Transport YT',
            'access_token' => 'mock_google_token',
            'is_active' => true,
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'platform_post_id' => 'vid_metro_route_5',
            'title' => 'New Metro Route 5 Expansion',
        ]);

        Http::fake([
            'https://www.googleapis.com/youtube/v3/commentThreads*' => Http::response([
                'items' => [
                    [
                        'id' => 'yt_ctrl_comm_555',
                        'snippet' => [
                            'videoId' => 'vid_metro_route_5',
                            'topLevelComment' => [
                                'id' => 'yt_ctrl_comm_555',
                                'snippet' => [
                                    'authorDisplayName' => 'Commuter Priya',
                                    'textDisplay' => 'Great news for daily commuters! Thank you so much.',
                                    'likeCount' => 14,
                                    'publishedAt' => '2026-11-04T10:15:00Z',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.comments.sync'), [
            'account_id' => $account->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'account_id' => $account->id,
            'platform_comment_id' => 'yt_ctrl_comm_555',
            'commenter_name' => 'Commuter Priya',
            'likes_count' => 14,
        ]);
    }

    public function test_reply_to_youtube_comment_publishes_to_youtube(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_YOUTUBE,
            'youtube_channel_id' => 'UC_reply_yt_chan',
            'name' => 'City Council Info',
            'access_token' => 'mock_google_token',
            'is_active' => true,
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'platform_post_id' => 'vid_budget_2026',
            'title' => 'Budget Hearing',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'platform_comment_id' => 'yt_parent_comment_123',
            'commenter_name' => 'Citizen Amit',
            'message' => 'Where can we review the line-by-line pdf?',
            'status' => Comment::STATUS_PENDING_REVIEW,
        ]);

        Http::fake([
            'https://www.googleapis.com/youtube/v3/comments*' => Http::response([
                'id' => 'yt_reply_id_9999',
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.comments.reply', $comment->id), [
            'reply_message' => 'You can download the full PDF at city.gov/budget2026',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(Comment::STATUS_REPLIED, $comment->fresh()->status);
        $this->assertEquals('You can download the full PDF at city.gov/budget2026', $comment->fresh()->ai_reply);
        $this->assertEquals('yt_reply_id_9999', $comment->fresh()->meta_reply_id);
    }

    public function test_process_comment_job_when_auto_reply_mode_is_off_discards_ai_reply(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_auto_off_99',
            'name' => 'Auto Off Page',
            'access_token' => 'mock_token',
            'auto_reply_mode' => Account::AUTO_REPLY_OFF, // Mode 0: Off
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'fb_p_off_1',
            'message' => 'Post message',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'meta_comm_off_1',
            'commenter_name' => 'Citizen Sharma',
            'message' => 'Great initiative sir!',
            'status' => Comment::STATUS_NEW,
        ]);

        (new \App\Jobs\ProcessCommentJob($comment))->handle();

        $comment->refresh();
        $this->assertEquals('positive', $comment->sentiment);
        $this->assertNull($comment->ai_reply); // Should NOT have an ai_reply when mode is Off
        $this->assertEquals(Comment::STATUS_PENDING_REVIEW, $comment->status);
    }

    public function test_process_comment_job_when_auto_reply_mode_is_auto_approved_publishes_live(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_auto_appr_88',
            'name' => 'Auto Approved Page',
            'access_token' => 'mock_token',
            'auto_reply_mode' => Account::AUTO_REPLY_AUTO_APPROVED, // Mode 2: Auto comment and auto approved
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'fb_p_appr_2',
            'message' => 'Post message',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'meta_comm_appr_2',
            'commenter_name' => 'Citizen Verma',
            'message' => 'Shandar karya sir ji!',
            'status' => Comment::STATUS_NEW,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/meta_comm_appr_2/comments' => Http::response([
                'id' => 'live_reply_published_888',
            ], 200),
        ]);

        $mockAi = \Mockery::mock(\App\Contracts\AIServiceInterface::class);
        $mockAi->shouldReceive('isConfigured')->andReturn(true);
        $mockAi->shouldReceive('analyzeAndDraftReply')->andReturn([
            'sentiment' => 'positive',
            'sentiment_score' => 0.9,
            'category' => 'appreciation',
            'is_abusive' => false,
            'abuse_confidence' => 0.05,
            'is_complaint' => false,
            'is_favor' => true,
            'is_against' => false,
            'ai_reply' => 'Dhanyawad Verma ji aapke samarthan ke liye!',
        ]);
        $this->app->instance(\App\Contracts\AIServiceInterface::class, $mockAi);

        (new \App\Jobs\ProcessCommentJob($comment))->handle();

        $comment->refresh();
        $this->assertEquals('positive', $comment->sentiment);
        $this->assertNotNull($comment->ai_reply);
        $this->assertEquals(Comment::STATUS_REPLIED, $comment->status); // Auto published live!
        $this->assertNotNull($comment->replied_at);
    }

    public function test_account_settings_updates_auto_reply_mode_select(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_settings_test',
            'name' => 'Ward Page',
            'access_token' => 'mock_token',
            'auto_reply_mode' => Account::AUTO_REPLY_WITH_APPROVAL,
        ]);

        $response = $this->actingAs($user)->post(route('admin.accounts.update-settings', $account->id), [
            'ai_tone' => 'Humble & Respectful',
            'ai_language' => 'Hindi + Hinglish',
            'auto_reply_mode' => 2, // Auto comment and auto approved
            'auto_hide_trolls' => 1,
            'comment_to_dm' => 0,
        ]);

        $response->assertRedirect();
        $account->refresh();
        $this->assertEquals(Account::AUTO_REPLY_AUTO_APPROVED, $account->auto_reply_mode);
        $this->assertTrue($account->auto_hide_trolls);
    }

    public function test_comments_filter_by_date(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_date_test',
            'name' => 'Date Filter Testing',
            'access_token' => 'mock_token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'post_d1',
            'message' => 'Post for comment date testing',
        ]);

        $commentOct = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'c_oct_1',
            'commenter_name' => 'Rahul Sharma',
            'message' => 'Comment from October 10',
            'commented_at' => '2026-10-10 12:00:00',
        ]);

        $commentNov = Comment::create([
            'post_id' => $post->id,
            'account_id' => $account->id,
            'meta_comment_id' => 'c_nov_1',
            'commenter_name' => 'Pooja Verma',
            'message' => 'Comment from November 15',
            'commented_at' => '2026-11-15 09:30:00',
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.comments.facebook.data', ['date' => '2026-11-15']));

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringContainsString('Pooja Verma', $content);
        $this->assertStringNotContainsString('Rahul Sharma', $content);
    }
}

