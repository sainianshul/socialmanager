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

        $bjp = PoliticalParty::where('short_code', 'BJP')->first();

        $samplePosters = [
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
                'category_slug'   => 'environment',
                'name'            => 'Ek Ped Maa Ke Naam - Tree Plantation',
                'slug'            => 'ek-ped-maa-ke-naam',
                'file'            => 'ek-ped-maa-ke-naam.jpg',
                'default_caption' => "पर्यावरण संरक्षण के संकल्प के साथ, आइए माँ के सम्मान में एक पौधा अवश्य लगाएं और उसकी रक्षा करें। #EkPedMaaKeNaam #GreenIndia #Environment",
                'show_on_top'     => false,
                'party_id'        => null,
                'sort_order'      => 1,
            ],
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
        ];

        // Clean up any old obsolete posters with hindi names
        Poster::whereNotIn('slug', array_column($samplePosters, 'slug'))->delete();

        foreach ($samplePosters as $data) {
            $category = PosterCategory::where('slug', $data['category_slug'])->first();
            if (!$category) {
                continue;
            }

            $srcFile = public_path('images/posters/' . $data['file']);
            $destRel = 'uploads/posters/' . $data['file'];
            $destFull = Storage::disk('public')->path($destRel);

            if (File::exists($srcFile) && !File::exists($destFull)) {
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
