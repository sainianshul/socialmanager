<?php

namespace Tests\Feature;

use App\Models\Festival;
use App\Models\User;
use Database\Seeders\FestivalSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FestivalStudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_festival_studio_requires_authentication(): void
    {
        $response = $this->get(route('admin.festival-studio.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_festival_studio(): void
    {
        $this->seed(FestivalSeeder::class);

        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.festival-studio.index'));

        $response->assertStatus(200);
        $response->assertSee('Festival Studio');
        $response->assertSee('Samvidhan Diwas');
    }

    public function test_sidebar_contains_festival_studio_for_regular_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.posts.create'));

        $response->assertStatus(200);
        $response->assertSee('Festival Studio');
        $response->assertSee(route('admin.festival-studio.index'));
    }

    public function test_festival_studio_ajax_returns_json_content(): void
    {
        $this->seed(FestivalSeeder::class);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $festival = Festival::first();

        $response = $this->actingAs($user)->getJson(
            route('admin.festival-studio.index', ['festival_id' => $festival->id]),
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'festival_id' => $festival->id,
            'title' => $festival->title,
        ]);
        $response->assertJsonStructure([
            'success',
            'festival_id',
            'title',
            'festival_date',
            'caption',
            'hashtags',
            'html',
        ]);
    }
}
