<?php

namespace Database\Seeders;

use App\Models\PoliticalParty;
use App\Models\Poster;
use App\Models\PosterCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PosterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('uploads/posters');

        $samplePosters = [
            // 1. Social Awareness
            [
                'category_slug'   => 'social-awareness',
                'name'            => 'Beti Bachao Beti Padhao',
                'slug'            => 'beti-bachao-beti-padhao',
                'file'            => 'beti-bachao-beti-padhao.jpg',
                'default_caption' => "बेटियां हैं हमारे देश का गौरव और भविष्य। आइए, बेटियों को पढ़ाएं, आगे बढ़ाएं और सशक्त बनाएं। #BetiBachaoBetiPadhao #WomenEmpowerment #NationalPride",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 1,
            ],
            [
                'category_slug'   => 'social-awareness',
                'name'            => 'Swachh Bharat Abhiyan',
                'slug'            => 'swachh-bharat-swasth-bharat',
                'file'            => 'swachh-bharat-swasth-bharat.jpg',
                'default_caption' => "स्वच्छता ही सेवा है। अपने घर, गली और शहर को स्वच्छ रखें, स्वस्थ राष्ट्र के निर्माण में भागीदार बनें। #SwachhBharat #CleanIndia #HealthFirst",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 2,
            ],
            [
                'category_slug'   => 'social-awareness',
                'name'            => 'Save Water, Save Life - Jal Sanrakshan',
                'slug'            => 'save-water-save-life',
                'file'            => 'save-water-save-life.jpg',
                'default_caption' => "जल ही जीवन है, इसका हर एक कतरा अनमोल है। आइए, पानी की बर्बादी रोकें और जल संरक्षण को जन आंदोलन बनाएं। #SaveWater #JalShakti #WaterConservation",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 3,
            ],

            // 2. Daily Quotes & Suvichar
            [
                'category_slug'   => 'suvichar',
                'name'            => 'Daily Suvichar - Karma & Success',
                'slug'            => 'dainik-suvichar-karma',
                'file'            => 'dainik-suvichar-karma.jpg',
                'default_caption' => "सफलता की शुरुआत सकारात्मक सोच और निरंतर कर्म से होती है। आपका दिन शुभ और मंगलमय हो! #Suvichar #DailyQuotes #Motivation #GoodMorning",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 1,
            ],

            // 3. Birthday & Anniversary
            [
                'category_slug'   => 'birthday-anniversary',
                'name'            => 'Happy Birthday Wishes',
                'slug'            => 'janmadin-shubhkamnaye',
                'file'            => 'janmadin-shubhkamnaye.jpg',
                'default_caption' => "आपको जन्मदिन की अनंत शुभकामनाएं! ईश्वर से आपके उत्तम स्वास्थ्य, दीर्घायु एवं उज्ज्वल भविष्य की मंगल कामना करता हूँ। #HappyBirthday #BestWishes",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 1,
            ],

            // 4. Condolence & Tribute
            [
                'category_slug'   => 'shok-sandesh',
                'name'            => 'Bhavpurna Shradhanjali - Condolence & Tribute',
                'slug'            => 'bhavpurna-shradhanjali',
                'file'            => 'bhavpurna-shradhanjali.jpg',
                'default_caption' => "पुण्यात्मा को शत-शत नमन एवं भावभीनी श्रद्धांजलि। ईश्वर दिवंगत आत्मा को अपने श्रीचरणों में स्थान दें तथा शोकाकुल परिवार को यह असह्य दुःख सहन करने की शक्ति प्रदान करें। ॐ शांति!",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 1,
            ],

            // 5. National & Patriotic
            [
                'category_slug'   => 'patriotic',
                'name'            => 'Vande Mataram - National Pride & Tiranga',
                'slug'            => 'vande-mataram-tiranga',
                'file'            => 'vande-mataram-tiranga.jpg',
                'default_caption' => "विजयी विश्व तिरंगा प्यारा, झंडा ऊंचा रहे हमारा। भारत माता के सभी वीर सपूतों को कोटि-कोटि नमन। जय हिन्द, वन्दे मातरम्! #VandeMataram #JaiHind #Tiranga",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 1,
            ],
            [
                'category_slug'   => 'patriotic',
                'name'            => 'India Gate - National Pride & Amar Jawan',
                'slug'            => 'india-gate-national-pride',
                'file'            => 'india-gate-national-pride.jpg',
                'default_caption' => "राष्ट्र रक्षा में अपने प्राणों का सर्वोच्च बलिदान देने वाले अमर बलिदानी वीरों को कोटि-कोटि नमन। वन्दे मातरम्! जय हिन्द! #IndiaGate #AmarJawan #IndianArmy #SaluteTheHeroes",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 2,
            ],
            [
                'category_slug'   => 'patriotic',
                'name'            => 'Statue of Unity - Rashtriya Ekta Diwas',
                'slug'            => 'statue-of-unity-ekta',
                'file'            => 'statue-of-unity-ekta.jpg',
                'default_caption' => "अखंड भारत के निर्माता, लौह पुरुष सरदार वल्लभभाई पटेल जी को शत्-शत् नमन। 'एक भारत, श्रेष्ठ भारत' के संकल्प के साथ आगे बढ़ें। #StatueOfUnity #SardarPatel #EkBharatShreshthaBharat",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 3,
            ],

            // 6. Environment & Nature
            [
                'category_slug'   => 'environment',
                'name'            => 'Ek Ped Maa Ke Naam - Tree Plantation',
                'slug'            => 'ek-ped-maa-ke-naam',
                'file'            => 'ek-ped-maa-ke-naam.jpg',
                'default_caption' => "पर्यावरण संरक्षण के संकल्प के साथ, आइए माँ के सम्मान में एक पौधा अवश्य लगाएं और उसकी रक्षा करें। #EkPedMaaKeNaam #GreenIndia #Environment",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 1,
            ],

            // 7. Spiritual & Devotion
            [
                'category_slug'   => 'spiritual-devotion',
                'name'            => 'Jai Shri Ram - Ayodhya Ram Mandir',
                'slug'            => 'jai-shri-ram-ayodhya',
                'file'            => 'jai-shri-ram-ayodhya.jpg',
                'default_caption' => "मंगल भवन अमंगल हारी, द्रवहु सुदसरथ अजिर बिहारी। मर्यादा पुरुषोत्तम प्रभु श्री राम की कृपा आप सभी पर सदैव बनी रहे। जय श्री राम! #JaiShriRam #AyodhyaRamMandir #RamLalla",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 1,
            ],
            [
                'category_slug'   => 'spiritual-devotion',
                'name'            => 'Har Har Mahadev - Divine Adiyogi Shiva',
                'slug'            => 'har-har-mahadev-adiyogi',
                'file'            => 'har-har-mahadev-adiyogi.jpg',
                'default_caption' => "कर्पूरगौरं करुणावतारं संसारसारं भुजगेन्द्रहारम्। देवाधिदेव महादेव भगवान शिव की कृपा से आपके जीवन में सुख, शांति एवं समृद्धि का वास हो। हर हर महादेव! #HarHarMahadev #Adiyogi #BholeNath",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 2,
            ],
            [
                'category_slug'   => 'spiritual-devotion',
                'name'            => 'Harmandir Sahib - Golden Temple Amritsar',
                'slug'            => 'golden-temple-amritsar',
                'file'            => 'golden-temple-amritsar.jpg',
                'default_caption' => "सतिगुर नानक प्रगट्या मिटी धुंधु जगि चानणु होआ। श्री हरिमंदिर साहिब (स्वर्ण मंदिर) के पावन दर्शन। वाहेगुरु जी का खालसा, वाहेगुरु जी की फतेह! #GoldenTemple #HarmandirSahib #Amritsar #Waheguru",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 3,
            ],

            // 8. Farmers & Agriculture
            [
                'category_slug'   => 'kisan-kalyan',
                'name'            => 'Kisan Samman - Annadata Pride',
                'slug'            => 'kisan-samman-annadata',
                'file'            => 'kisan-samman-annadata.jpg',
                'default_caption' => "खेतों में पसीना बहाकर देश को समृद्ध बनाने वाले हमारे अन्नदाता किसान भाइयों को कोटि-कोटि नमन। जय जवान, जय किसान! #KisanSamman #Annadata #IndianFarmers #JaiKisan",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 1,
            ],

            // 9. Health & Wellness
            [
                'category_slug'   => 'health-blood-donation',
                'name'            => 'International Yoga Day & Wellness',
                'slug'            => 'international-yoga-day',
                'file'            => 'international-yoga-day.jpg',
                'default_caption' => "'योगः कर्मसु कौशलम्' - योग केवल व्यायाम नहीं, बल्कि स्वस्थ तन और शांत मन की साधना है। नियमित योग करें, निरोगी रहें। #InternationalYogaDay #YogaForWellness #FitIndia",
                'show_on_top'     => true,
                'party_id'        => null,
                'sort_order'      => 1,
            ],
        ];

        // Clean up any obsolete posters
        Poster::whereNotIn('slug', array_column($samplePosters, 'slug'))->delete();

        foreach ($samplePosters as $data) {
            $category = PosterCategory::where('slug', $data['category_slug'])->first();
            if (! $category) {
                continue;
            }

            $srcFile = public_path('images/posters/' . $data['file']);
            $destRel = 'uploads/posters/' . $data['file'];
            $destFull = Storage::disk('public')->path($destRel);

            if (File::exists($srcFile) && ! File::exists($destFull)) {
                File::copy($srcFile, $destFull);
            }

            Poster::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id'     => $category->id,
                    'party_id'        => $data['party_id'],
                    'name'            => $data['name'],
                    'image_path'      => $destRel,
                    'thumbnail_path'  => $destRel,
                    'default_caption' => $data['default_caption'],
                    'show_on_top'     => $data['show_on_top'],
                    'sort_order'      => $data['sort_order'],
                    'is_active'       => true,
                ]
            );
        }
    }
}
