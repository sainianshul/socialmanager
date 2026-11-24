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
                'name' => 'सामाजिक जागरूकता (Social Awareness)',
                'slug' => 'social-awareness',
                'icon' => 'ti ti-heart-handshake',
                'show_as_collection' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'दैनिक सुविचार एवं प्रेरक विचार (Suvichar & Quotes)',
                'slug' => 'suvichar',
                'icon' => 'ti ti-sun',
                'show_as_collection' => true,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'शोक संदेश एवं विनम्र श्रद्धांजलि (Condolence & Tribute)',
                'slug' => 'shok-sandesh',
                'icon' => 'ti ti-candle',
                'show_as_collection' => true,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'शुभकामनाएं एवं बधाई (Greetings & Congratulations)',
                'slug' => 'greetings',
                'icon' => 'ti ti-confetti',
                'show_as_collection' => true,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'राष्ट्रीय एवं देशभक्ति (National & Patriotic)',
                'slug' => 'patriotic',
                'icon' => 'ti ti-flag',
                'show_as_collection' => false,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'सरकारी योजनाएं एवं विकास कार्य (Govt Schemes & Development)',
                'slug' => 'govt-schemes',
                'icon' => 'ti ti-building-estate',
                'show_as_collection' => false,
                'sort_order' => 6,
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
