<?php

namespace Tests\Feature;

use App\Models\PoliticalParty;
use App\Models\Poster;
use App\Models\PosterCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReadymadePosterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->regularUser = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_can_create_poster_category_and_posters(): void
    {
        $category = PosterCategory::create([
            'name' => 'सामाजिक जागरूकता',
            'slug' => 'social-awareness',
            'icon' => 'ti ti-heart-handshake',
            'show_as_collection' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('poster_categories', [
            'slug' => 'social-awareness',
            'show_as_collection' => true,
        ]);

        $party = PoliticalParty::where('short_code', 'BJP')->first() ?? PoliticalParty::create([
            'name' => 'Bharatiya Janata Party',
            'short_code' => 'BJP',
            'color' => '#FF9933',
        ]);

        // Universal Poster
        $universalPoster = Poster::create([
            'category_id' => $category->id,
            'party_id' => null,
            'name' => 'बेटी बचाओ बेटी पढ़ाओ',
            'slug' => 'beti-bachao',
            'image_path' => 'posters/beti_bachao.jpg',
            'default_caption' => 'बेटी बचाओ बेटी पढ़ाओ #BetiBachao',
            'show_on_top' => true,
            'usage_count' => 15,
        ]);

        // Party-specific Poster
        $partyPoster = Poster::create([
            'category_id' => $category->id,
            'party_id' => $party->id,
            'name' => 'विकसित भारत संकल्प',
            'slug' => 'viksit-bharat',
            'image_path' => 'posters/viksit_bharat.jpg',
            'usage_count' => 45,
        ]);

        $this->assertCount(2, $category->posters);
        $this->assertEquals($category->name, $universalPoster->category->name);

        // Test Scopes
        $universalOnly = Poster::forParty(null)->get();
        $this->assertTrue($universalOnly->contains($universalPoster));
        $this->assertFalse($universalOnly->contains($partyPoster));

        $bjpWithUniversal = Poster::forParty($party->id)->get();
        $this->assertCount(2, $bjpWithUniversal);

        // Test Trending scope
        $trending = Poster::trending()->first();
        $this->assertEquals('विकसित भारत संकल्प', $trending->name);
    }

    public function test_admin_sidebar_contains_readymade_posters_and_categories(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Readymade Posters');
        $response->assertSee('Categories');
        $response->assertSee('Posters');
        $response->assertSee(route('admin.poster-categories.index'));
        $response->assertSee(route('admin.posters.index'));
    }

    public function test_admin_can_manage_poster_categories(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('admin.poster-categories.index'));
        $response->assertStatus(200);

        // 2. Create
        $response = $this->actingAs($this->admin)->get(route('admin.poster-categories.create'));
        $response->assertStatus(200);

        // 3. Store
        $response = $this->actingAs($this->admin)->post(route('admin.poster-categories.store'), [
            'name' => 'शोक संदेश एवं श्रद्धांजलि',
            'slug' => 'shok-sandesh',
            'icon' => 'ti ti-candle',
            'show_as_collection' => 1,
            'sort_order' => 2,
            'is_active' => 1,
        ]);
        $response->assertRedirect(route('admin.poster-categories.index'));

        $category = PosterCategory::where('slug', 'shok-sandesh')->firstOrFail();
        $this->assertEquals('शोक संदेश एवं श्रद्धांजलि', $category->name);
        $this->assertTrue($category->show_as_collection);

        // 4. Edit
        $response = $this->actingAs($this->admin)->get(route('admin.poster-categories.edit', $category));
        $response->assertStatus(200);

        // 5. Update
        $response = $this->actingAs($this->admin)->put(route('admin.poster-categories.update', $category), [
            'name' => 'विनम्र श्रद्धांजलि',
            'slug' => 'shok-sandesh',
            'icon' => 'ti ti-candle',
            'show_as_collection' => 0,
            'sort_order' => 5,
            'is_active' => 1,
        ]);
        $response->assertRedirect(route('admin.poster-categories.index'));

        $category->refresh();
        $this->assertEquals('विनम्र श्रद्धांजलि', $category->name);
        $this->assertFalse($category->show_as_collection);

        // 6. Destroy
        $response = $this->actingAs($this->admin)->delete(route('admin.poster-categories.destroy', $category));
        $response->assertRedirect(route('admin.poster-categories.index'));
        $this->assertDatabaseMissing('poster_categories', ['id' => $category->id]);
    }

    public function test_admin_can_manage_readymade_posters(): void
    {
        Storage::fake('public');

        $category = PosterCategory::create([
            'name' => 'दैनिक सुविचार',
            'slug' => 'daily-suvichar',
            'is_active' => true,
        ]);

        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('admin.posters.index'));
        $response->assertStatus(200);

        // 2. Create
        $response = $this->actingAs($this->admin)->get(route('admin.posters.create'));
        $response->assertStatus(200);

        // 3. Store
        $file = UploadedFile::fake()->image('suvichar.jpg', 1080, 1080);
        $response = $this->actingAs($this->admin)->post(route('admin.posters.store'), [
            'category_id' => $category->id,
            'party_id' => null, // Universal
            'name' => 'सकारात्मक सोच ही सफलता की कुंजी है',
            'poster_image' => $file,
            'default_caption' => 'शुभ प्रभात! सकारात्मक सोच ही सफलता की कुंजी है। #Suvichar',
            'show_on_top' => 1,
            'is_active' => 1,
        ]);
        $response->assertRedirect(route('admin.posters.index', ['category_id' => $category->id]));

        $poster = Poster::where('category_id', $category->id)->firstOrFail();
        $this->assertEquals('सकारात्मक सोच ही सफलता की कुंजी है', $poster->name);
        $this->assertNull($poster->party_id);
        $this->assertTrue($poster->show_on_top);
        Storage::disk('public')->assertExists($poster->image_path);

        // 4. Edit
        $response = $this->actingAs($this->admin)->get(route('admin.posters.edit', $poster));
        $response->assertStatus(200);

        // 5. Update
        $response = $this->actingAs($this->admin)->put(route('admin.posters.update', $poster), [
            'category_id' => $category->id,
            'party_id' => null,
            'name' => 'आज का सुविचार - सफलता',
            'default_caption' => 'Updated Caption',
            'show_on_top' => 0,
            'is_active' => 1,
        ]);
        $response->assertRedirect(route('admin.posters.index', ['category_id' => $category->id]));

        $poster->refresh();
        $this->assertEquals('आज का सुविचार - सफलता', $poster->name);
        $this->assertFalse($poster->show_on_top);

        // 6. Destroy
        $response = $this->actingAs($this->admin)->delete(route('admin.posters.destroy', $poster));
        $response->assertRedirect(route('admin.posters.index'));
        $this->assertDatabaseMissing('posters', ['id' => $poster->id]);
    }

    public function test_regular_user_cannot_manage_readymade_posters(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.poster-categories.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->regularUser)->get(route('admin.posters.index'));
        $response->assertStatus(403);
    }

    public function test_readymade_posters_api_list_returns_active_categories_and_posters(): void
    {
        $category = PosterCategory::create([
            'name' => 'सामाजिक जागरूकता',
            'slug' => 'social-awareness',
            'is_active' => true,
        ]);

        $poster = Poster::create([
            'category_id' => $category->id,
            'name' => 'बेटी बचाओ',
            'slug' => 'beti-bachao',
            'image_path' => 'uploads/posters/sample.jpg',
            'default_caption' => 'Beti Bachao Caption',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->regularUser)->get(route('admin.readymade-posters.list'));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment(['name' => 'बेटी बचाओ']);
        $response->assertJsonFragment(['name' => 'सामाजिक जागरूकता']);
    }

    public function test_user_can_schedule_post_with_readymade_poster_and_increments_usage_count(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('uploads/posters/beti_bachao.jpg', 'fake-image-content');

        $category = PosterCategory::create([
            'name' => 'जागरूकता',
            'slug' => 'awareness',
            'is_active' => true,
        ]);

        $poster = Poster::create([
            'category_id' => $category->id,
            'name' => 'बेटी बचाओ बेटी पढ़ाओ',
            'slug' => 'beti-bachao-beti-padhao',
            'image_path' => 'uploads/posters/beti_bachao.jpg',
            'default_caption' => 'Official Beti Bachao Caption',
            'usage_count' => 0,
            'is_active' => true,
        ]);

        $account = \App\Models\Account::create([
            'user_id' => $this->regularUser->id,
            'account_type' => \App\Models\Account::TYPE_FACEBOOK,
            'account_id' => '1234567890',
            'name' => 'Test FB Page',
            'status' => 1,
            'access_token' => 'dummy_token',
        ]);

        $response = $this->actingAs($this->regularUser)->post(route('admin.posts.schedule.store'), [
            'platform' => 1,
            'account_id' => $account->id,
            'post_type' => 'photo',
            'content' => 'Support women empowerment!',
            'publish_mode' => 'schedule',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'readymade_poster_paths' => ['uploads/posters/beti_bachao.jpg'],
            'readymade_poster_id' => $poster->id,
        ]);

        $response->assertRedirect(route('admin.posts.schedule'));

        $scheduledPost = \App\Models\ScheduledPost::where('user_id', $this->regularUser->id)->firstOrFail();
        $this->assertContains('uploads/posters/beti_bachao.jpg', $scheduledPost->media_urls);

        $poster->refresh();
        $this->assertEquals(1, $poster->usage_count);
    }
}
