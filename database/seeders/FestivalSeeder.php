<?php

namespace Database\Seeders;

use App\Models\Festival;
use Illuminate\Database\Seeder;

class FestivalSeeder extends Seeder
{
    public function run(): void
    {
        $festivals = [
            [
                'title' => 'Dr. B.R. Ambedkar Jayanti',
                'festival_date' => '2026-04-14',
                'category' => 'jayanti_punyatithi',
                'ready_caption' => "भारतीय संविधान के मुख्य शिल्पकार, भारत रत्न बोधिसत्व डॉ. भीमराव अम्बेडकर जी की जयंती पर उन्हें कोटि-कोटि नमन एवं आप सभी को हार्दिक शुभकामनाएं।\n\n'शिक्षित बनो, संगठित रहो, संघर्ष करो।' - बाबासाहेब",
                'hashtags' => '#AmbedkarJayanti #Babasaheb #DrAmbedkar #JaiBhim',
                'is_active' => true,
            ],
            [
                'title' => 'Mahavir Jayanti',
                'festival_date' => '2026-04-15',
                'category' => 'religious_festival',
                'ready_caption' => "सत्य, अहिंसा, त्याग और करुणा के अमर संदेशवाहक जैन धर्म के 24वें तीर्थंकर भगवान महावीर स्वामी जी के जन्म कल्याणक (महावीर जयंती) की आप सभी को हार्दिक शुभकामनाएं।\n\n'जियो और जीने दो' का पावन संदेश हम सभी के जीवन में शांति और सौहार्द का संचार करे।",
                'hashtags' => '#MahavirJayanti #BhagwanMahavir #Ahimsa #Jainism',
                'is_active' => true,
            ],
            [
                'title' => 'Ram Navami',
                'festival_date' => '2026-04-17',
                'category' => 'religious_festival',
                'ready_caption' => "मर्यादा पुरुषोत्तम भगवान श्री राम के जन्मोत्सव 'श्री राम नवमी' के पावन पर्व की आप सभी को हार्दिक बधाई एवं शुभकामनाएं।\n\nप्रभु श्री राम की कृपा से आपके जीवन में सुख, शांति, समृद्धि और उत्तम स्वास्थ्य का वास हो। जय श्री राम!",
                'hashtags' => '#RamNavami #JaiShriRam #ShriRamNavami #Ayodhya',
                'is_active' => true,
            ],
            [
                'title' => 'World Yoga Day',
                'festival_date' => '2026-06-21',
                'category' => 'international_day',
                'ready_caption' => "समस्त देशवासियों को अंतर्राष्ट्रीय योग दिवस की हार्दिक शुभकामनाएं।\n\nयोग केवल व्यायाम नहीं, बल्कि स्वयं से जुड़ने, मन को शांत और शरीर को निरोगी रखने की एक दिव्य जीवनशैली है। आइए, नियमित योग को अपनी दिनचर्या का हिस्सा बनाएं।",
                'hashtags' => '#InternationalYogaDay #YogaDay2026 #HealthAndFitness #YogaForLife',
                'is_active' => true,
            ],
            [
                'title' => 'Independence Day',
                'festival_date' => '2026-08-15',
                'category' => 'national_day',
                'ready_caption' => "समस्त देशवासियों को 80वें स्वतंत्रता दिवस की हार्दिक शुभकामनाएं!\n\nदेश की आजादी के लिए अपने प्राणों की आहुति देने वाले सभी अमर शहीदों एवं स्वतंत्रता सेनानियों को शत-शत नमन। आइए हम सब मिलकर एक सशक्त, समृद्ध और आत्मनिर्भर भारत के निर्माण का संकल्प लें। भारत माता की जय! वन्दे मातरम्!",
                'hashtags' => '#IndependenceDay #SwatantrataDiwas #15August #VandeMataram #JaiHind',
                'is_active' => true,
            ],
            [
                'title' => 'Gandhi Jayanti & Lal Bahadur Shastri Jayanti',
                'festival_date' => '2026-10-02',
                'category' => 'jayanti_punyatithi',
                'ready_caption' => "सत्य और अहिंसा के पुजारी राष्ट्रपिता महात्मा गांधी जी एवं 'जय जवान, जय किसान' के उद्घोषक पूर्व प्रधानमंत्री लाल बहादुर शास्त्री जी की जयंती पर उन्हें विनम्र श्रद्धांजलि एवं शत-शत नमन।",
                'hashtags' => '#GandhiJayanti #LalBahadurShastri #MahatmaGandhi #JaiJawanJaiKisan',
                'is_active' => true,
            ],
            [
                'title' => 'Diwali - Deepawali',
                'festival_date' => '2026-11-08',
                'category' => 'religious_festival',
                'ready_caption' => "प्रकाश, उमंग और खुशियों के महापर्व दीपावली की आप सभी को हार्दिक शुभकामनाएं!\n\nमां लक्ष्मी और भगवान श्री गणेश जी की कृपा से आपके घर-आंगन में सदैव सुख, समृद्धि, ऐश्वर्य और शांति का वास हो। शुभ दीपावली!",
                'hashtags' => '#HappyDiwali #Deepawali #Diwali2026 #FestivalOfLights #SubhDeepavali',
                'is_active' => true,
            ],
            [
                'title' => 'Republic Day',
                'festival_date' => '2027-01-26',
                'category' => 'national_day',
                'ready_caption' => "समस्त देशवासियों को गणतंत्र दिवस की हार्दिक बधाई एवं शुभकामनाएं।\n\nआइए, हम सभी मिलकर भारत के संविधान के मूल्यों की रक्षा करने तथा देश की एकता, अखंडता और संप्रभुता को अक्षुण्ण बनाए रखने का संकल्प लें। जय हिन्द! वन्दे मातरम्!",
                'hashtags' => '#RepublicDay #GantantraDiwas #26January #JaiHind #IndianConstitution',
                'is_active' => true,
            ],
        ];

        foreach ($festivals as $fest) {
            Festival::updateOrCreate(
                ['title' => $fest['title'], 'festival_date' => $fest['festival_date']],
                $fest
            );
        }
    }
}
