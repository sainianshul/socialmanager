<?php

namespace Tests\Feature;

use App\Contracts\AIServiceInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class YouTubeAiDescriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_generate_ai_description(): void
    {
        $response = $this->postJson(route('admin.posts.generate-ai-description'), [
            'title' => 'My First Video',
        ]);

        $response->assertStatus(401);
    }

    public function test_validation_requires_title(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('admin.posts.generate-ai-description'), [
            'title' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_authenticated_user_generates_seo_description_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('admin.posts.generate-ai-description'), [
            'title' => 'Complete Roadmap to Web Development 2026',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['description']);
        $this->assertGreaterThan(50, $data['character_count']);
        // Verify SEO elements: contains keywords and hashtags
        $this->assertMatchesRegularExpression('/roadmap|development/i', $data['description']);
        $this->assertStringContainsString('#', $data['description']);
    }

    public function test_ai_service_custom_mock_is_called_when_configured(): void
    {
        $user = User::factory()->create();

        $mockAI = $this->createMock(AIServiceInterface::class);
        $mockAI->method('isConfigured')->willReturn(true);
        $mockAI->method('generateVideoDescription')->willReturn(
            "AI Generated SEO YouTube Description\n\nIn this video, we explore budget updates.\n\n#Budget #Updates"
        );

        $this->app->instance(AIServiceInterface::class, $mockAI);

        $response = $this->actingAs($user)->postJson(route('admin.posts.generate-ai-description'), [
            'title' => 'Budget 2026 Full Analysis',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'description' => "AI Generated SEO YouTube Description\n\nIn this video, we explore budget updates.\n\n#Budget #Updates",
        ]);
    }

    public function test_create_post_view_contains_ai_description_button(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.posts.create'));

        $response->assertStatus(200);
        $response->assertSee('Generate Description From AI');
        $response->assertSee('btn-generate-ai-desc');
    }
}
