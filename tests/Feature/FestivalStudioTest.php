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
        $response->assertSee('Upcoming Festivals', false);
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
}
