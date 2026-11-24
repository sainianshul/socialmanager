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
                'name' => 'दैनिक सुविचार एवं प्रेरक विचार (Daily Quotes & Suvichar)',
                'slug' => 'suvichar',
                'icon' => 'ti ti-sun',
                'show_as_collection' => true,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'जन्मदिन एवं वर्षगांठ शुभकामनाएं (Birthday & Anniversary)',
                'slug' => 'birthday-anniversary',
                'icon' => 'ti ti-cake',
                'show_as_collection' => true,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'शोक संदेश एवं विनम्र श्रद्धांजलि (Condolence & Tribute)',
                'slug' => 'shok-sandesh',
                'icon' => 'ti ti-candle',
                'show_as_collection' => true,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'शुभकामनाएं एवं बधाई (Greetings & Congratulations)',
                'slug' => 'greetings',
                'icon' => 'ti ti-confetti',
                'show_as_collection' => true,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'राजनीतिक एवं संगठनात्मक कार्यक्रम (Political & Org Events)',
                'slug' => 'political-events',
                'icon' => 'ti ti-users-group',
                'show_as_collection' => true,
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'राष्ट्रीय एवं देशभक्ति (National & Patriotic)',
                'slug' => 'patriotic',
                'icon' => 'ti ti-flag',
                'show_as_collection' => true,
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'महिला सशक्तिकरण एवं नारी शक्ति (Women Empowerment)',
                'slug' => 'nari-shakti',
                'icon' => 'ti ti-woman',
                'show_as_collection' => false,
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'किसान कल्याण एवं अन्नदाता (Farmers & Agriculture)',
                'slug' => 'kisan-kalyan',
                'icon' => 'ti ti-plant-2',
                'show_as_collection' => false,
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'धार्मिक एवं आध्यात्मिक विचार (Spiritual & Devotion)',
                'slug' => 'spiritual-devotion',
                'icon' => 'ti ti-bell-ringing',
                'show_as_collection' => false,
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'सरकारी योजनाएं एवं विकास कार्य (Govt Schemes & Development)',
                'slug' => 'govt-schemes',
                'icon' => 'ti ti-building-estate',
                'show_as_collection' => false,
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'पर्यावरण एवं प्रकृति संरक्षण (Environment & Nature)',
                'slug' => 'environment',
                'icon' => 'ti ti-leaf',
                'show_as_collection' => false,
                'sort_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'स्वास्थ्य एवं रक्तदान जागरूकता (Health & Blood Donation)',
                'slug' => 'health-blood-donation',
                'icon' => 'ti ti-first-aid-kit',
                'show_as_collection' => false,
                'sort_order' => 13,
                'is_active' => true,
            ],
            [
                'name' => 'युवा एवं खेल कूद (Youth & Sports)',
                'slug' => 'youth-sports',
                'icon' => 'ti ti-trophy',
                'show_as_collection' => false,
                'sort_order' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'व्यापार एवं आत्मनिर्भर भारत (Local Business & MSME)',
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
