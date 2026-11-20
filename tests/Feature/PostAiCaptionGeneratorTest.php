<?php

namespace Tests\Feature;

use App\Contracts\AIServiceInterface;
use App\Models\Account;
use App\Models\ScheduledPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostAiCaptionGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_generate_ai_caption(): void
    {
        $response = $this->postJson(route('admin.posts.generate-ai-caption'), [
            'topic' => 'Our new product launch',
        ]);

        $response->assertStatus(401);
    }

    public function test_validation_requires_topic(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('admin.posts.generate-ai-caption'), [
            'topic' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['topic']);
    }

    public function test_authenticated_user_generates_ai_caption_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('admin.posts.generate-ai-caption'), [
            'topic'    => 'Announcing our revolutionary summer collection 2026',
            'platform' => 'instagram',
            'tone'     => 'engaging',
            'language' => 'English',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['caption']);
        $this->assertNotEmpty($data['hook']);
        $this->assertIsArray($data['hashtags']);
        $this->assertNotEmpty($data['hashtags']);
        $this->assertIsArray($data['variations']);
        $this->assertCount(3, $data['variations']);
        $this->assertStringContainsString('#', $data['full_text']);
    }

    public function test_ai_service_custom_mock_is_called_when_configured(): void
    {
        $user = User::factory()->create();

        $mockAI = $this->createMock(AIServiceInterface::class);
        $mockAI->method('isConfigured')->willReturn(true);
        $mockAI->method('generateSocialCaption')->willReturn([
            'hook'           => 'Are you ready for the ultimate upgrade?',
            'caption'        => "Big news today! We are unveiling our latest innovation.\n\nTell us what you think below!",
            'call_to_action' => 'Tell us what you think below!',
            'hashtags'       => ['#Innovation', '#TechTrends', '#Growth'],
            'variations'     => [
                ['title' => 'Story', 'text' => 'Custom Story text'],
                ['title' => 'Punchy', 'text' => 'Custom Punchy text'],
            ],
            'full_text'      => "Big news today! #Innovation #TechTrends #Growth",
        ]);

        $this->app->instance(AIServiceInterface::class, $mockAI);

        $response = $this->actingAs($user)->postJson(route('admin.posts.generate-ai-caption'), [
            'topic' => 'Latest innovation launch',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'hook'    => 'Are you ready for the ultimate upgrade?',
            'caption' => "Big news today! We are unveiling our latest innovation.\n\nTell us what you think below!",
            'hashtags'=> ['#Innovation', '#TechTrends', '#Growth'],
        ]);
    }

    public function test_create_and_edit_post_views_contain_ai_caption_button(): void
    {
        $user = User::factory()->create();

        $account = Account::create([
            'user_id'         => $user->id,
            'account_type'    => Account::TYPE_FACEBOOK,
            'meta_account_id' => 'fb_page_999',
            'name'            => 'Official Brand Page',
            'access_token'    => 'mock_token_abc',
            'is_active'       => true,
        ]);

        // Create view
        $responseCreate = $this->actingAs($user)->get(route('admin.posts.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('AI Caption & Hashtags', false);
        $responseCreate->assertSee('btn-toggle-ai-caption');

        // Edit view
        $post = ScheduledPost::create([
            'user_id'      => $user->id,
            'account_id'   => $account->id,
            'platform'     => Account::TYPE_FACEBOOK,
            'post_type'    => 'photo',
            'content'      => 'Initial post caption',
            'status'       => 'scheduled',
            'scheduled_at' => now()->addDay(),
        ]);

        $responseEdit = $this->actingAs($user)->get(route('admin.posts.schedule.edit', $post));
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('AI Caption & Hashtags', false);
        $responseEdit->assertSee('btn-toggle-ai-caption');
    }
}
