<?php

namespace Tests\Feature;

use App\Jobs\ProcessCommentJob;
use App\Models\Account;
use App\Models\AutoDmRule;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AutoDmWebhookTriggerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Account $facebookAccount;
    private Account $instagramAccount;
    private Post $facebookPost;
    private Post $instagramPost;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->facebookAccount = Account::create([
            'user_id'         => $this->user->id,
            'account_type'    => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_1001',
            'name'            => 'Official Politician Page',
            'access_token'    => 'fb_page_access_token_xyz',
            'is_active'       => true,
            'webhook_active'  => true,
        ]);

        $this->instagramAccount = Account::create([
            'user_id'                       => $this->user->id,
            'account_type'                  => Account::TYPE_INSTAGRAM,
            'meta_account_id'               => 'ig_user_2002',
            'instagram_business_account_id' => 'ig_user_2002',
            'name'                          => 'Official Politician IG',
            'access_token'                  => 'ig_page_access_token_xyz',
            'is_active'                     => true,
            'webhook_active'                => true,
        ]);

        $this->facebookPost = Post::create([
            'account_id'   => $this->facebookAccount->id,
            'meta_post_id' => 'fb_post_sample_111',
            'platform'     => Account::TYPE_FACEBOOK,
            'caption'      => 'General Facebook Update',
        ]);

        $this->instagramPost = Post::create([
            'account_id'   => $this->instagramAccount->id,
            'meta_post_id' => 'ig_media_sample_222',
            'platform'     => Account::TYPE_INSTAGRAM,
            'caption'      => 'General Instagram Photo',
        ]);

        // Use mock AI to use local smart analyzer instantly with zero network latency
        $mockAi = $this->createMock(\App\Contracts\AIServiceInterface::class);
        $mockAi->method('isConfigured')->willReturn(false);
        $this->app->instance(\App\Contracts\AIServiceInterface::class, $mockAi);
    }

    public function test_incoming_comment_triggers_matching_facebook_auto_dm_rule(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/fb_comm_123/private_replies*' => Http::response([
                'id' => 'mid_dm_777',
            ], 200),
            'https://graph.facebook.com/v20.0/fb_comm_123/comments*' => Http::response([
                'id' => 'fb_public_reply_888',
            ], 200),
        ]);

        $rule = AutoDmRule::create([
            'user_id'      => $this->user->id,
            'account_id'   => $this->facebookAccount->id,
            'name'         => 'Price & Link Automation',
            'keywords'     => ['price', 'link'],
            'match_type'   => 'contains',
            'dm_message'   => 'Namaste! Here is the link you requested: https://example.com/details',
            'public_reply' => 'Dhanyawad! Humein aapko DM bhej diya hai, check karein.',
            'is_active'    => true,
        ]);

        $comment = Comment::create([
            'account_id'        => $this->facebookAccount->id,
            'post_id'           => $this->facebookPost->id,
            'meta_comment_id'   => 'fb_comm_123',
            'commenter_name'    => 'Sunil Sharma',
            'commenter_meta_id' => 'constituent_meta_555',
            'message'           => 'Vidhayak ji kripya link aur price provide karein',
            'status'            => Comment::STATUS_NEW,
            'commented_at'      => now(),
        ]);

        // Process incoming comment
        ProcessCommentJob::dispatchSync($comment);

        $rule->refresh();
        $comment->refresh();

        $this->assertEquals(1, $rule->trigger_count);
        $this->assertNotNull($rule->last_triggered_at);
        $this->assertEquals(Comment::STATUS_REPLIED, $comment->status);
        $this->assertNotNull($comment->replied_at);
        $this->assertEquals('Dhanyawad! Humein aapko DM bhej diya hai, check karein.', $comment->ai_reply);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'fb_comm_123/private_replies') &&
                   $request['message'] === 'Namaste! Here is the link you requested: https://example.com/details';
        });

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'fb_comm_123/comments') &&
                   $request['message'] === 'Dhanyawad! Humein aapko DM bhej diya hai, check karein.';
        });
    }

    public function test_incoming_comment_triggers_instagram_auto_dm_rule(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*/messages*' => Http::response([
                'message_id' => 'mid_dm_999',
            ], 200),
            'https://graph.facebook.com/v20.0/*/private_replies*' => Http::response([
                'id' => 'mid_dm_999',
            ], 200),
        ]);

        $rule = AutoDmRule::create([
            'user_id'      => $this->user->id,
            'account_id'   => $this->instagramAccount->id,
            'name'         => 'Instagram Details Rule',
            'keywords'     => ['details'],
            'match_type'   => 'contains',
            'dm_message'   => 'Here are the campaign details directly in your inbox.',
            'public_reply' => null,
            'is_active'    => true,
        ]);

        $comment = Comment::create([
            'account_id'        => $this->instagramAccount->id,
            'post_id'           => $this->instagramPost->id,
            'meta_comment_id'   => 'ig_comm_456',
            'commenter_name'    => 'priya_verma',
            'commenter_meta_id' => 'ig_constituent_333',
            'message'           => 'Please send more details',
            'status'            => Comment::STATUS_NEW,
            'commented_at'      => now(),
        ]);

        ProcessCommentJob::dispatchSync($comment);

        $rule->refresh();
        $comment->refresh();

        $this->assertEquals(1, $rule->trigger_count);
        $this->assertNotNull($rule->last_triggered_at);
        $this->assertNotNull($comment->replied_at);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/messages') || str_contains($request->url(), 'private_replies');
        });
    }

    public function test_incoming_comment_does_not_trigger_when_keywords_do_not_match(): void
    {
        Http::fake();

        $rule = AutoDmRule::create([
            'user_id'      => $this->user->id,
            'account_id'   => $this->facebookAccount->id,
            'name'         => 'Keyword Specific Rule',
            'keywords'     => ['register', 'apply'],
            'match_type'   => 'contains',
            'dm_message'   => 'Registration link sent.',
            'is_active'    => true,
        ]);

        $comment = Comment::create([
            'account_id'        => $this->facebookAccount->id,
            'post_id'           => $this->facebookPost->id,
            'meta_comment_id'   => 'fb_comm_nomatch',
            'commenter_name'    => 'Aman Verma',
            'commenter_meta_id' => 'user_111',
            'message'           => 'Bahut badhiya kaam kiya aapne sir ji!',
            'status'            => Comment::STATUS_NEW,
            'commented_at'      => now(),
        ]);

        ProcessCommentJob::dispatchSync($comment);

        $rule->refresh();
        $this->assertEquals(0, $rule->trigger_count);
        $this->assertNull($rule->last_triggered_at);

        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'private_replies');
        });
    }

    public function test_incoming_comment_does_not_trigger_when_rule_is_inactive(): void
    {
        Http::fake();

        $rule = AutoDmRule::create([
            'user_id'      => $this->user->id,
            'account_id'   => $this->facebookAccount->id,
            'name'         => 'Paused Automation Rule',
            'keywords'     => ['link'],
            'match_type'   => 'contains',
            'dm_message'   => 'Should not send this DM.',
            'is_active'    => false, // Inactive
        ]);

        $comment = Comment::create([
            'account_id'        => $this->facebookAccount->id,
            'post_id'           => $this->facebookPost->id,
            'meta_comment_id'   => 'fb_comm_paused',
            'commenter_name'    => 'Rahul',
            'commenter_meta_id' => 'user_222',
            'message'           => 'Send link please',
            'status'            => Comment::STATUS_NEW,
            'commented_at'      => now(),
        ]);

        ProcessCommentJob::dispatchSync($comment);

        $rule->refresh();
        $this->assertEquals(0, $rule->trigger_count);

        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'private_replies');
        });
    }

    public function test_specific_post_rule_prioritized_over_all_posts_rule(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/fb_comm_priority/private_replies*' => Http::response([
                'id' => 'mid_dm_priority',
            ], 200),
        ]);

        $post = Post::create([
            'account_id'   => $this->facebookAccount->id,
            'meta_post_id' => 'fb_post_9999',
            'platform'     => Account::TYPE_FACEBOOK,
            'caption'      => 'Scholarship Scheme 2026',
        ]);

        // Specific post rule
        $specificRule = AutoDmRule::create([
            'user_id'      => $this->user->id,
            'account_id'   => $this->facebookAccount->id,
            'post_id'      => $post->id,
            'name'         => 'Scholarship Specific Rule',
            'keywords'     => ['scheme'],
            'match_type'   => 'contains',
            'dm_message'   => 'Scholarship portal link: https://scholarship.gov/apply',
            'is_active'    => true,
        ]);

        // General all-posts rule
        $generalRule = AutoDmRule::create([
            'user_id'      => $this->user->id,
            'account_id'   => $this->facebookAccount->id,
            'post_id'      => null, // All Posts
            'name'         => 'General Campaign Rule',
            'keywords'     => ['scheme'],
            'match_type'   => 'contains',
            'dm_message'   => 'General party website link',
            'is_active'    => true,
        ]);

        $comment = Comment::create([
            'account_id'        => $this->facebookAccount->id,
            'post_id'           => $post->id,
            'meta_comment_id'   => 'fb_comm_priority',
            'commenter_name'    => 'Student Citizen',
            'commenter_meta_id' => 'student_333',
            'message'           => 'Kripya scheme ki jaankari dein',
            'status'            => Comment::STATUS_NEW,
            'commented_at'      => now(),
        ]);

        ProcessCommentJob::dispatchSync($comment);

        $specificRule->refresh();
        $generalRule->refresh();

        // Specific post rule should have triggered
        $this->assertEquals(1, $specificRule->trigger_count);
        // General rule should NOT have triggered
        $this->assertEquals(0, $generalRule->trigger_count);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'fb_comm_priority/private_replies') &&
                   $request['message'] === 'Scholarship portal link: https://scholarship.gov/apply';
        });
    }

    public function test_incoming_comment_does_not_trigger_auto_dm_for_toxic_or_abusive_comment(): void
    {
        Http::fake();

        $rule = AutoDmRule::create([
            'user_id'      => $this->user->id,
            'account_id'   => $this->facebookAccount->id,
            'name'         => 'Promo Link Rule',
            'keywords'     => ['link'],
            'match_type'   => 'contains',
            'dm_message'   => 'Promo DM link',
            'is_active'    => true,
        ]);

        $comment = Comment::create([
            'account_id'        => $this->facebookAccount->id,
            'post_id'           => $this->facebookPost->id,
            'meta_comment_id'   => 'fb_comm_abuse',
            'commenter_name'    => 'Troll Account',
            'commenter_meta_id' => 'troll_999',
            'message'           => 'chor lutera fraud idiot send link',
            'status'            => Comment::STATUS_NEW,
            'commented_at'      => now(),
        ]);

        ProcessCommentJob::dispatchSync($comment);

        $rule->refresh();
        $comment->refresh();

        // Abusive comment should be flagged and hidden
        $this->assertTrue((bool) $comment->is_abusive);
        $this->assertTrue((bool) $comment->should_hide);
        $this->assertEquals(0, $rule->trigger_count);

        // No DM sent to abusive users
        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'private_replies');
        });
    }

    public function test_incoming_comment_does_not_trigger_for_page_own_comment(): void
    {
        Http::fake();

        $rule = AutoDmRule::create([
            'user_id'      => $this->user->id,
            'account_id'   => $this->facebookAccount->id,
            'name'         => 'Self Comment Safeguard',
            'keywords'     => ['link'],
            'match_type'   => 'contains',
            'dm_message'   => 'Link message',
            'is_active'    => true,
        ]);

        // Comment posted by the page itself
        $comment = Comment::create([
            'account_id'        => $this->facebookAccount->id,
            'post_id'           => $this->facebookPost->id,
            'meta_comment_id'   => 'fb_comm_self',
            'commenter_name'    => 'Official Politician Page',
            'commenter_meta_id' => 'fb_page_1001', // Same as account meta_account_id
            'message'           => 'Here is the link for everyone',
            'status'            => Comment::STATUS_NEW,
            'commented_at'      => now(),
        ]);

        ProcessCommentJob::dispatchSync($comment);

        $rule->refresh();
        $this->assertEquals(0, $rule->trigger_count);

        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'private_replies');
        });
    }

    public function test_auto_dm_personalizes_message_with_commenter_name(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/fb_comm_pers/private_replies*' => Http::response([
                'id' => 'mid_dm_pers_11',
            ], 200),
            'https://graph.facebook.com/v20.0/fb_comm_pers/comments*' => Http::response([
                'id' => 'fb_reply_pers_22',
            ], 200),
        ]);

        $rule = AutoDmRule::create([
            'user_id'      => $this->user->id,
            'account_id'   => $this->facebookAccount->id,
            'name'         => 'Personalized Rule',
            'keywords'     => ['scheme'],
            'match_type'   => 'contains',
            'dm_message'   => 'Namaste {name}, thank you for asking!',
            'public_reply' => 'Dhanyawad {first_name} ji, check your inbox.',
            'is_active'    => true,
        ]);

        $comment = Comment::create([
            'account_id'        => $this->facebookAccount->id,
            'post_id'           => $this->facebookPost->id,
            'meta_comment_id'   => 'fb_comm_pers',
            'commenter_name'    => 'Amitabh Verma',
            'commenter_meta_id' => 'constituent_meta_789',
            'message'           => 'Please share details of this scheme',
            'status'            => Comment::STATUS_NEW,
            'commented_at'      => now(),
        ]);

        ProcessCommentJob::dispatchSync($comment);

        $rule->refresh();
        $comment->refresh();

        $this->assertEquals(1, $rule->trigger_count);
        $this->assertEquals('Dhanyawad Amitabh ji, check your inbox.', $comment->ai_reply);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'fb_comm_pers/private_replies') &&
                   $request['message'] === 'Namaste Amitabh Verma, thank you for asking!';
        });

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'fb_comm_pers/comments') &&
                   $request['message'] === 'Dhanyawad Amitabh ji, check your inbox.';
        });
    }
}
