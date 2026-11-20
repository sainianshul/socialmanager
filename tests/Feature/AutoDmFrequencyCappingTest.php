<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AutoDmRecipient;
use App\Models\AutoDmRule;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\MetaMessagingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AutoDmFrequencyCappingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_receives_first_auto_dm_and_subsequent_dms_within_24h_are_capped(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_capping',
            'name' => 'Auto DM Capping Page',
            'access_token' => 'valid_token_123',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'fb_post_cap',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Download Ebook',
            'keywords' => ['link'],
            'dm_message' => 'Here is your link: https://example.com/guide',
            'public_reply' => 'Link sent to DM!',
            'is_active' => true,
        ]);

        // Mock Meta API
        Http::fake([
            'https://graph.facebook.com/v20.0/c_first/private_replies' => Http::response(['id' => 'msg_101'], 200),
            'https://graph.facebook.com/v20.0/c_first/comments' => Http::response(['id' => 'rep_101'], 200),
            'https://graph.facebook.com/v20.0/c_second/private_replies' => Http::response(['id' => 'msg_102'], 200),
        ]);

        $service = new MetaMessagingService('v20.0');

        // First comment from user "Alice" (Meta ID: alice_meta_999)
        $comment1 = Comment::create([
            'account_id' => $account->id,
            'post_id' => $post->id,
            'meta_comment_id' => 'c_first',
            'commenter_name' => 'Alice',
            'commenter_meta_id' => 'alice_meta_999',
            'message' => 'Please send link',
        ]);

        $res1 = $service->sendAutoDm($rule, $comment1);

        $this->assertTrue($res1['success']);
        $this->assertTrue($res1['dm_sent']);

        // Verify recipient record exists in database
        $this->assertDatabaseHas('auto_dm_recipients', [
            'account_id' => $account->id,
            'commenter_id' => 'alice_meta_999',
            'dm_count' => 1,
        ]);

        // Second comment from same user "Alice" 5 minutes later
        $comment2 = Comment::create([
            'account_id' => $account->id,
            'post_id' => $post->id,
            'meta_comment_id' => 'c_second',
            'commenter_name' => 'Alice',
            'commenter_meta_id' => 'alice_meta_999',
            'message' => 'Send link again please',
        ]);

        $res2 = $service->sendAutoDm($rule, $comment2);

        // Second attempt must be frequency capped!
        $this->assertFalse($res2['success']);
        $this->assertFalse($res2['dm_sent']);
        $this->assertTrue($res2['frequency_capped']);
        $this->assertStringContainsString('Frequency capped', $res2['message']);

        // Verify dm_count was not incremented
        $recipient = AutoDmRecipient::where('account_id', $account->id)
            ->where('commenter_id', 'alice_meta_999')
            ->first();
        $this->assertEquals(1, $recipient->dm_count);
    }

    public function test_different_users_are_not_blocked_by_each_others_capping(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_multi_users',
            'name' => 'Multi User Page',
            'access_token' => 'valid_token_456',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'p_multi',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Coupon Rule',
            'keywords' => ['coupon'],
            'dm_message' => 'Here is your 20% coupon: SAVE20',
            'is_active' => true,
        ]);

        Http::fake([
            '*' => Http::response(['id' => 'msg_ok'], 200),
        ]);

        $service = new MetaMessagingService('v20.0');

        // User 1: Ramesh
        $comment1 = Comment::create([
            'account_id' => $account->id,
            'post_id' => $post->id,
            'meta_comment_id' => 'c_ramesh',
            'commenter_name' => 'Ramesh',
            'commenter_meta_id' => 'ramesh_123',
            'message' => 'coupon code',
        ]);

        $res1 = $service->sendAutoDm($rule, $comment1);
        $this->assertTrue($res1['success']);

        // User 2: Suresh (different user)
        $comment2 = Comment::create([
            'account_id' => $account->id,
            'post_id' => $post->id,
            'meta_comment_id' => 'c_suresh',
            'commenter_name' => 'Suresh',
            'commenter_meta_id' => 'suresh_456',
            'message' => 'coupon please',
        ]);

        $res2 = $service->sendAutoDm($rule, $comment2);
        $this->assertTrue($res2['success']);
    }

    public function test_user_can_receive_dm_again_after_24_hours(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_expired_cap',
            'name' => 'Expired Cap Page',
            'access_token' => 'valid_token_789',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'p_expired',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Info Rule',
            'keywords' => ['info'],
            'dm_message' => 'Info link',
            'is_active' => true,
        ]);

        // Pre-create recipient with last_dm_sent_at 25 hours ago
        AutoDmRecipient::create([
            'account_id' => $account->id,
            'commenter_id' => 'deepak_789',
            'last_dm_sent_at' => now()->subHours(25),
            'dm_count' => 1,
        ]);

        Http::fake([
            '*' => Http::response(['id' => 'msg_ok_after_25h'], 200),
        ]);

        $service = new MetaMessagingService('v20.0');

        $comment = Comment::create([
            'account_id' => $account->id,
            'post_id' => $post->id,
            'meta_comment_id' => 'c_deepak_new',
            'commenter_name' => 'Deepak',
            'commenter_meta_id' => 'deepak_789',
            'message' => 'need info',
        ]);

        $res = $service->sendAutoDm($rule, $comment);
        $this->assertTrue($res['success']);
        $this->assertTrue($res['dm_sent']);

        // Check dm_count incremented to 2
        $this->assertEquals(2, AutoDmRecipient::where('commenter_id', 'deepak_789')->first()->dm_count);
    }
}
