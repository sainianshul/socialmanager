<?php

namespace Tests\Feature;

use App\Jobs\ProcessCommentJob;
use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\GeminiAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MetaWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_meta_webhook_get_verification_successful(): void
    {
        $response = $this->get('/webhook/meta?hub.mode=subscribe&hub.verify_token=warroom_secret_token_123&hub.challenge=test_challenge_code_999');

        $response->assertStatus(200);
        $this->assertEquals('test_challenge_code_999', $response->getContent());
    }

    public function test_meta_webhook_get_verification_fails_with_invalid_token(): void
    {
        $response = $this->get('/webhook/meta?hub.mode=subscribe&hub.verify_token=wrong_token&hub.challenge=test_challenge_code_999');

        $response->assertStatus(403);
    }

    public function test_meta_webhook_post_receives_comment_and_dispatches_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '1404772002715952',
            'name' => 'Healthiiiii',
            'access_token' => 'test_token',
            'is_active' => true,
        ]);

        $payload = [
            'object' => 'page',
            'entry' => [
                [
                    'id' => '1404772002715952',
                    'time' => 1730210000,
                    'changes' => [
                        [
                            'field' => 'feed',
                            'value' => [
                                'item' => 'comment',
                                'verb' => 'add',
                                'comment_id' => 'test_webhook_comment_123',
                                'post_id' => '1404772002715952_post_999',
                                'message' => 'Vidhayak ji bahut shandar kaam kiya aapne!',
                                'created_time' => 1730210000,
                                'from' => [
                                    'id' => 'constituent_meta_id_888',
                                    'name' => 'Ramesh Kumar',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/webhook/meta', $payload);

        $response->assertStatus(200);
        $this->assertEquals('EVENT_RECEIVED', $response->getContent());

        // Assert comment created in database
        $this->assertDatabaseHas('comments', [
            'meta_comment_id' => 'test_webhook_comment_123',
            'account_id' => $account->id,
            'commenter_name' => 'Ramesh Kumar',
            'message' => 'Vidhayak ji bahut shandar kaam kiya aapne!',
        ]);

        // Assert job dispatched
        Queue::assertPushed(ProcessCommentJob::class, function ($job) {
            return $job->comment->meta_comment_id === 'test_webhook_comment_123';
        });
    }

    public function test_gemini_ai_service_analyzes_and_drafts_reply(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'sentiment' => 'positive',
                                        'sentiment_score' => 0.95,
                                        'category' => 'appreciation',
                                        'is_abusive' => false,
                                        'abuse_confidence' => 0.01,
                                        'is_complaint' => false,
                                        'is_favor' => true,
                                        'is_against' => false,
                                        'ai_reply' => 'Ramesh ji, aapke aashirwad aur vishwas ke liye hardik dhanyawad! Jai Hind.',
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        config(['services.gemini.api_key' => 'mock_gemini_key_123']);

        $service = app(GeminiAIService::class);
        $result = $service->analyzeAndDraftReply('Shandar karyakram Netaji!', [
            'commenter_name' => 'Ramesh',
            'account_name' => 'Netaji Official',
            'tone' => 'Humble',
            'language' => 'Hinglish',
        ]);

        $this->assertNotNull($result);
        $this->assertEquals('positive', $result['sentiment']);
        $this->assertEquals('appreciation', $result['category']);
        $this->assertFalse($result['is_abusive']);
        $this->assertStringContainsString('Ramesh ji', $result['ai_reply']);
    }
}

