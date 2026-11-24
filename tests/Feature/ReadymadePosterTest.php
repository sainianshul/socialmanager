<?php

namespace Tests\Feature;

use App\Models\PoliticalParty;
use App\Models\Poster;
use App\Models\PosterCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadymadePosterTest extends TestCase
{
    use RefreshDatabase;

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
}
