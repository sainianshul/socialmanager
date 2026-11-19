<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\AutoDmRule;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\MetaMessagingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MetaMessagingServiceTest extends TestCase
{
    use RefreshDatabase;

    private MetaMessagingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MetaMessagingService('v20.0');
    }

    public function test_send_auto_dm_fails_when_account_has_no_token(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '123456',
            'name' => 'No Token Page',
            'access_token' => '',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'p_123',
        ]);

        $comment = Comment::create([
            'account_id' => $account->id,
            'post_id' => $post->id,
            'meta_comment_id' => 'c_123',
            'commenter_name' => 'John Doe',
            'message' => 'Please send link',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Ebook Rule',
            'keywords' => ['link'],
            'dm_message' => 'Here is the link: https://test.com',
            'is_active' => true,
        ]);

        $result = $this->service->sendAutoDm($rule, $comment);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('No active access token', $result['message']);
        $this->assertEquals(0, $rule->fresh()->trigger_count);
    }

    public function test_send_auto_dm_facebook_success(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_100',
            'name' => 'Active Facebook Page',
            'access_token' => 'valid_fb_token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'fb_post_200',
        ]);

        $comment = Comment::create([
            'account_id' => $account->id,
            'post_id' => $post->id,
            'meta_comment_id' => 'fb_comment_300',
            'commenter_name' => 'Alice',
            'message' => 'Interested in the ebook guide!',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'FB Guide DM',
            'keywords' => ['ebook', 'guide'],
            'dm_message' => 'Hey Alice! Here is your download: https://test.com/guide.pdf',
            'public_reply' => 'Sent to your DM! Check your inbox 📥',
            'is_active' => true,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/fb_comment_300/private_replies' => Http::response([
                'id' => 'm_mid_123456789',
            ], 200),
            'https://graph.facebook.com/v20.0/fb_comment_300/comments' => Http::response([
                'id' => 'public_reply_999',
            ], 200),
        ]);

        $result = $this->service->sendAutoDm($rule, $comment);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['dm_sent']);
        $this->assertEquals('m_mid_123456789', $result['dm_id']);
        $this->assertTrue($result['public_reply_sent']);
        $this->assertEquals('public_reply_999', $result['public_reply_id']);

        // Check rule trigger count & timestamp
        $rule->refresh();
        $this->assertEquals(1, $rule->trigger_count);
        $this->assertNotNull($rule->last_triggered_at);

        // Check comment status update
        $comment->refresh();
        $this->assertEquals(Comment::STATUS_REPLIED, $comment->status);
        $this->assertEquals('Sent to your DM! Check your inbox 📥', $comment->ai_reply);
        $this->assertEquals('public_reply_999', $comment->meta_reply_id);
        $this->assertNotNull($comment->replied_at);
    }

    public function test_send_auto_dm_instagram_success(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_act_500',
            'instagram_business_account_id' => 'ig_biz_555',
            'name' => 'Insta Brand',
            'access_token' => 'valid_ig_token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'ig_post_600',
        ]);

        $comment = Comment::create([
            'account_id' => $account->id,
            'post_id' => $post->id,
            'meta_comment_id' => 'ig_comment_700',
            'commenter_name' => 'Bob',
            'message' => 'Price please',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'IG Price DM',
            'keywords' => ['price'],
            'dm_message' => 'The special promo price is $49!',
            'public_reply' => 'Sent details to your DM!',
            'is_active' => true,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/ig_biz_555/messages' => Http::response([
                'message_id' => 'ig_msg_8888',
            ], 200),
            'https://graph.facebook.com/v20.0/ig_comment_700/replies' => Http::response([
                'id' => 'ig_reply_7777',
            ], 200),
        ]);

        $result = $this->service->sendAutoDm($rule, $comment);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['dm_sent']);
        $this->assertEquals('ig_msg_8888', $result['dm_id']);
        $this->assertTrue($result['public_reply_sent']);
        $this->assertEquals('ig_reply_7777', $result['public_reply_id']);

        $this->assertEquals(1, $rule->fresh()->trigger_count);
    }

    public function test_send_auto_dm_instagram_fallback_success(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => 'ig_act_900',
            'name' => 'Insta Brand Fallback',
            'access_token' => 'valid_token',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'p_900',
        ]);

        $comment = Comment::create([
            'account_id' => $account->id,
            'post_id' => $post->id,
            'meta_comment_id' => 'ig_comm_999',
            'commenter_name' => 'Charlie',
            'message' => 'Info please',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Fallback Rule',
            'keywords' => ['info'],
            'dm_message' => 'Here is all the info you need!',
            'public_reply' => null, // No public reply
            'is_active' => true,
        ]);

        // Primary messages endpoint returns error, but fallback private_replies succeeds
        Http::fake([
            'https://graph.facebook.com/v20.0/ig_act_900/messages' => Http::response([
                'error' => ['message' => 'Cannot use direct recipient without conversation'],
            ], 400),
            'https://graph.facebook.com/v20.0/ig_comm_999/private_replies' => Http::response([
                'id' => 'fallback_dm_id_123',
            ], 200),
        ]);

        $result = $this->service->sendAutoDm($rule, $comment);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['dm_sent']);
        $this->assertEquals('fallback_dm_id_123', $result['dm_id']);
        $this->assertFalse($result['public_reply_sent']);
    }

    public function test_send_auto_dm_handles_already_replied_error(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_555',
            'name' => 'Duplicate Test Page',
            'access_token' => 'fb_tok',
        ]);

        $post = Post::create([
            'account_id' => $account->id,
            'meta_post_id' => 'p_555',
        ]);

        $comment = Comment::create([
            'account_id' => $account->id,
            'post_id' => $post->id,
            'meta_comment_id' => 'fb_comm_duplicated',
            'commenter_name' => 'David',
            'message' => 'link please',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'No Repeat Rule',
            'keywords' => ['link'],
            'dm_message' => 'Link sent',
            'is_active' => true,
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/fb_comm_duplicated/private_replies' => Http::response([
                'error' => [
                    'code' => 10900,
                    'message' => '(#10900) You cannot send a message to this comment because a private reply was already sent.',
                ],
            ], 400),
        ]);

        $result = $this->service->sendAutoDm($rule, $comment);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('already sent', $result['message']);
        $this->assertEquals(0, $rule->fresh()->trigger_count);
    }
}
