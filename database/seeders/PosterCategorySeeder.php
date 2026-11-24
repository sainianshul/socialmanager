<?php

namespace Database\Seeders;

use App\Models\PosterCategory;
use Illuminate\Database\Seeder;

class PosterCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Social Awareness',
                'slug' => 'social-awareness',
                'icon' => 'ti ti-heart-handshake',
                'show_as_collection' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Daily Quotes & Suvichar',
                'slug' => 'suvichar',
                'icon' => 'ti ti-sun',
                'show_as_collection' => true,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Birthday & Anniversary',
                'slug' => 'birthday-anniversary',
                'icon' => 'ti ti-cake',
                'show_as_collection' => true,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Condolence & Tribute',
                'slug' => 'shok-sandesh',
                'icon' => 'ti ti-candle',
                'show_as_collection' => true,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Greetings & Congratulations',
                'slug' => 'greetings',
                'icon' => 'ti ti-confetti',
                'show_as_collection' => true,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Political & Organization Events',
                'slug' => 'political-events',
                'icon' => 'ti ti-users-group',
                'show_as_collection' => true,
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'National & Patriotic',
                'slug' => 'patriotic',
                'icon' => 'ti ti-flag',
                'show_as_collection' => true,
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Women Empowerment',
                'slug' => 'nari-shakti',
                'icon' => 'ti ti-woman',
                'show_as_collection' => false,
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Farmers & Agriculture',
                'slug' => 'kisan-kalyan',
                'icon' => 'ti ti-plant-2',
                'show_as_collection' => false,
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Spiritual & Devotion',
                'slug' => 'spiritual-devotion',
                'icon' => 'ti ti-bell-ringing',
                'show_as_collection' => false,
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Govt Schemes & Development',
                'slug' => 'govt-schemes',
                'icon' => 'ti ti-building-estate',
                'show_as_collection' => false,
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Environment & Nature',
                'slug' => 'environment',
                'icon' => 'ti ti-leaf',
                'show_as_collection' => false,
                'sort_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Health & Blood Donation',
                'slug' => 'health-blood-donation',
                'icon' => 'ti ti-first-aid-kit',
                'show_as_collection' => false,
                'sort_order' => 13,
                'is_active' => true,
            ],
            [
                'name' => 'Youth & Sports',
                'slug' => 'youth-sports',
                'icon' => 'ti ti-trophy',
                'show_as_collection' => false,
                'sort_order' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Business & Self-Reliant India',
                'slug' => 'aatmanirbhar-bharat',
                'icon' => 'ti ti-shopping-bag',
                'show_as_collection' => false,
                'sort_order' => 15,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            PosterCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
