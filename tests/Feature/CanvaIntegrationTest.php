<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CanvaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_canva_endpoints(): void
    {
        $response = $this->get(route('admin.canva.status'));
        $response->assertRedirect(route('login'));

        $authResponse = $this->get(route('admin.canva.auth'));
        $authResponse->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_check_canva_status(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('admin.canva.status'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'is_configured',
            'is_connected',
            'auth_url',
            'templates' => [
                '*' => [
                    'id',
                    'name',
                    'dimensions',
                    'width',
                    'height',
                    'canva_url',
                ],
            ],
        ]);
        $this->assertTrue($response->json('is_configured'));
    }

    public function test_canva_auth_redirects_to_canva_with_pkce_challenge(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.canva.auth'));

        $response->assertStatus(302);
        $targetUrl = $response->headers->get('Location');

        $this->assertStringContainsString('canva.com/api/oauth/authorize', $targetUrl);
        $this->assertStringContainsString('code_challenge=', $targetUrl);
        $this->assertStringContainsString('code_challenge_method=S256', $targetUrl);
        $this->assertStringContainsString('client_id=' . config('services.canva.client_id'), $targetUrl);
    }

    public function test_canva_callback_handles_user_cancellation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.canva.callback', [
            'error'             => 'access_denied',
            'error_description' => 'User declined permission',
        ]));

        $response->assertRedirect(route('admin.posts.create'));
        $response->assertSessionHas('error');
    }

    public function test_canva_callback_validates_state_mismatch(): void
    {
        $user = User::factory()->create();

        session([
            'canva_oauth_state'    => 'valid_state_123',
            'canva_oauth_verifier' => 'verifier_123',
        ]);

        $response = $this->actingAs($user)->get(route('admin.canva.callback', [
            'code'  => 'sample_auth_code',
            'state' => 'forged_state',
        ]));

        $response->assertRedirect(route('admin.posts.create'));
        $response->assertSessionHas('error');
    }

    public function test_create_design_fails_when_user_not_connected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('admin.canva.create-design'), [
            'title' => 'Instagram Post',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success'      => false,
            'is_connected' => false,
        ]);
    }

    public function test_create_design_succeeds_when_connected_with_canva(): void
    {
        $user = User::factory()->create();

        Cache::put('canva_token_' . $user->id, 'mock_access_token_abc', 3600);

        Http::fake([
            'https://api.canva.com/rest/v1/designs' => Http::response([
                'design' => [
                    'id'    => 'DAFVztcvd9z',
                    'title' => 'My Social Post',
                    'urls'  => [
                        'edit_url' => 'https://www.canva.com/api/design/mock_jwt/edit',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.canva.create-design'), [
            'title'  => 'My Social Post',
            'width'  => 1080,
            'height' => 1080,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'   => true,
            'design_id' => 'DAFVztcvd9z',
            'edit_url'  => 'https://www.canva.com/api/design/mock_jwt/edit',
        ]);
    }

    public function test_export_design_downloads_image_to_storage(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Cache::put('canva_token_' . $user->id, 'mock_access_token_abc', 3600);

        Http::fake([
            'https://api.canva.com/rest/v1/exports*' => Http::response([
                'job' => [
                    'id'     => 'exp_job_123',
                    'status' => 'success',
                    'urls'   => [
                        'https://export-download.canva.com/sample_image.png',
                    ],
                ],
            ], 200),
            'https://export-download.canva.com/*' => Http::response('fake_image_binary_data', 200),
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.canva.export-design'), [
            'design_id' => 'DAFVztcvd9z',
            'format'    => 'png',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $relativePath = $response->json('relative_path');
        $this->assertNotEmpty($relativePath);
        Storage::disk('public')->assertExists($relativePath);
    }

    public function test_post_create_view_renders_canva_button_and_modal(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.posts.create'));

        $response->assertStatus(200);
        $response->assertSee('Design on Canva', false);
        $response->assertSee('modal-canva-studio', false);
        $response->assertSee('Canva Design Studio', false);
        $response->assertSee('Square Post', false);
        $response->assertSee('Story / Reel', false);
        $response->assertSee('Landscape Banner', false);
        $response->assertSee('YouTube Thumbnail', false);
    }
}
