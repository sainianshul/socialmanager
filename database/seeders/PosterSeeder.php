<?php

namespace Database\Seeders;

use App\Models\Poster;
use App\Models\PosterCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PosterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $postersData = [
            // 1. Social Awareness Posters
            [
                'category_slug' => 'social-awareness',
                'name' => 'बेटी बचाओ, बेटी पढ़ाओ (Beti Bachao Beti Padhao)',
                'image_path' => 'images/posters/beti-bachao-beti-padhao.jpg',
                'thumbnail_path' => 'images/posters/beti-bachao-beti-padhao.jpg',
                'default_caption' => "बेटी बचाओ, बेटी पढ़ाओ! बेटियां हमारे देश का गौरव और उज्ज्वल भविष्य हैं। आइए, हर बेटी को शिक्षा, सुरक्षा और आगे बढ़ने का समान अवसर दें। #BetiBachaoBetiPadhao #GirlChild #WomenEmpowerment",
                'show_on_top' => true,
                'sort_order' => 1,
                'usage_count' => 52,
            ],
            [
                'category_slug' => 'social-awareness',
                'name' => 'स्वच्छ भारत, स्वस्थ भारत (Swachh Bharat Mission)',
                'image_path' => 'images/posters/swachh-bharat-swasth-bharat.jpg',
                'thumbnail_path' => 'images/posters/swachh-bharat-swasth-bharat.jpg',
                'default_caption' => "स्वच्छ भारत, स्वस्थ भारत! स्वच्छता केवल एक अभियान नहीं, बल्कि हम सबका पावन कर्तव्य और संस्कार है। आइए, अपने घर, मोहल्ले और देश को स्वच्छ और सुंदर बनाएं। #SwachhBharat #SwachhBharatAbhiyan #CleanIndiaGreenIndia",
                'show_on_top' => true,
                'sort_order' => 2,
                'usage_count' => 64,
            ],

            // 2. Daily Quotes & Suvichar
            [
                'category_slug' => 'suvichar',
                'name' => 'दैनिक सुविचार - कर्म और असली पहचान (Daily Suvichar)',
                'image_path' => 'images/posters/dainik-suvichar-karma.jpg',
                'thumbnail_path' => 'images/posters/dainik-suvichar-karma.jpg',
                'default_caption' => "सुप्रभात! 'कर्म वह आईना है जो हमारा असली चेहरा हमें दिखा देता है।' निस्वार्थ भाव से किया गया कर्म ही जीवन में सच्ची शांति और सफलता का मार्ग प्रशस्त करता है। आपका दिन शुभ और मंगलमय हो! #Suvichar #Suprabhat #DailyMotivation #GoodMorning",
                'show_on_top' => true,
                'sort_order' => 1,
                'usage_count' => 98,
            ],

            // 3. Shok Sandesh & Condolence
            [
                'category_slug' => 'shok-sandesh',
                'name' => 'भावपूर्ण श्रद्धांजलि (Bhavpurna Shradhanjali Tribute)',
                'image_path' => 'images/posters/bhavpurna-shradhanjali.jpg',
                'thumbnail_path' => 'images/posters/bhavpurna-shradhanjali.jpg',
                'default_caption' => "परमपिता परमेश्वर से दिवंगत पुण्यात्मा को अपने श्रीचरणों में स्थान देने तथा शोकाकुल परिजनों को इस अपार दुःख को सहन करने की शक्ति प्रदान करने की प्रार्थना करते हैं। ॐ शांति! भावपूर्ण श्रद्धांजलि। #BhavpurnaShradhanjali #OmShanti #Shradhanjali",
                'show_on_top' => true,
                'sort_order' => 1,
                'usage_count' => 41,
            ],

            // 4. Birthday & Anniversary Wishes
            [
                'category_slug' => 'birthday-anniversary',
                'name' => 'जन्मदिन की हार्दिक शुभकामनाएं (Royal Golden Birthday Wishes)',
                'image_path' => 'images/posters/janmadin-shubhkamnaye.jpg',
                'thumbnail_path' => 'images/posters/janmadin-shubhkamnaye.jpg',
                'default_caption' => "जन्मदिन की हार्दिक बधाई एवं अनंत शुभकामनाएं! ईश्वर से प्रार्थना है कि आपका जीवन सदैव सुख, उत्तम स्वास्थ्य, दीर्घायु और अपार सफलताओं से आलोकित रहे। #HappyBirthday #JanmadinMubarak #BestWishes",
                'show_on_top' => true,
                'sort_order' => 1,
                'usage_count' => 85,
            ],

            // 5. National & Patriotic
            [
                'category_slug' => 'patriotic',
                'name' => 'वंदे मातरम् - तिरंगा हमारा गौरव (National Pride & Tiranga)',
                'image_path' => 'images/posters/vande-mataram-tiranga.jpg',
                'thumbnail_path' => 'images/posters/vande-mataram-tiranga.jpg',
                'default_caption' => "विजयी विश्व तिरंगा प्यारा, झंडा ऊंचा रहे हमारा! समस्त देशवासियों को राष्ट्र के मान, सम्मान और स्वाभिमान के प्रतीक राष्ट्रध्वज तिरंगे को नमन। जय हिन्द! वन्दे मातरम्! #VandeMataram #JaiHind #Tiranga #ProudIndian",
                'show_on_top' => true,
                'sort_order' => 1,
                'usage_count' => 92,
            ],

            // 6. Environment & Nature
            [
                'category_slug' => 'environment',
                'name' => 'एक पेड़ माँ के नाम (Ek Ped Maa Ke Naam - Tree Plantation)',
                'image_path' => 'images/posters/ek-ped-maa-ke-naam.jpg',
                'thumbnail_path' => 'images/posters/ek-ped-maa-ke-naam.jpg',
                'default_caption' => "'एक पेड़ माँ के नाम' - प्रकृति का सम्मान, जीवन का वरदान! आइए, अपनी माँ के सम्मान में और भावी पीढ़ी के स्वच्छ पर्यावरण के लिए आज ही एक पौधा अवश्य लगाएं और उसकी देखभाल का संकल्प लें। #EkPedMaaKeNaam #SaveEnvironment #TreePlantation #GreenIndia",
                'show_on_top' => true,
                'sort_order' => 1,
                'usage_count' => 58,
            ],
        ];

        foreach ($postersData as $item) {
            $category = PosterCategory::where('slug', $item['category_slug'])->first();
            if (! $category) {
                continue;
            }

            Poster::updateOrCreate(
                [
                    'category_id' => $category->id,
                    'name' => $item['name'],
                ],
                [
                    'slug' => Str::slug($item['name']),
                    'image_path' => $item['image_path'],
                    'thumbnail_path' => $item['thumbnail_path'],
                    'default_caption' => $item['default_caption'],
                    'show_on_top' => $item['show_on_top'],
                    'sort_order' => $item['sort_order'],
                    'usage_count' => $item['usage_count'],
                    'is_active' => true,
                ]
            );
        }
    }
}
