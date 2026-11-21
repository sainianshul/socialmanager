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
                'title' => 'Samvidhan Diwas (Constitution Day)',
                'festival_date' => '2026-11-26',
                'category' => 'national_day',
                'ready_caption' => "समस्त देशवासियों को संविधान दिवस की हार्दिक शुभकामनाएं!\n\nविश्व के सबसे बड़े और जीवंत लोकतंत्र के आधार स्तंभ भारत के संविधान के शिल्पकारों को कोटि-कोटि नमन। आइए हम अपने मौलिक कर्तव्यों के पालन का संकल्प लें। जय हिन्द!",
                'hashtags' => '#ConstitutionDay #SamvidhanDiwas #IndianConstitution #JaiHind',
                'is_active' => true,
            ],
            [
                'title' => 'Guru Nanak Jayanti (Prakash Parv)',
                'festival_date' => '2026-11-24',
                'category' => 'religious_festival',
                'ready_caption' => "सिख पंथ के प्रथम गुरु, मानवता, सद्भाव और शांति के प्रकाश पुंज श्री गुरु नानक देव जी के प्रकाश पर्व (गुरु नानक जयंती) की आप सभी को लख-लख बधाइयां।\n\n'किरत करो, नाम जपो, वंड छको'।",
                'hashtags' => '#GuruNanakJayanti #PrakashParv #GuruNanakDevJi #Waheguru',
                'is_active' => true,
            ],
            [
                'title' => 'Christmas Day',
                'festival_date' => '2026-12-25',
                'category' => 'religious_festival',
                'ready_caption' => "Merry Christmas to all! May this joyous festival fill your life with boundless peace, joy, happiness, and good health.",
                'hashtags' => '#MerryChristmas #Christmas2026 #HappyHolidays',
                'is_active' => true,
            ],
            [
                'title' => 'New Year 2027',
                'festival_date' => '2027-01-01',
                'category' => 'international_day',
                'ready_caption' => "नव वर्ष 2027 की आप सभी को हार्दिक बधाई एवं अनंत शुभकामनाएं!\n\nयह नया साल आपके जीवन में नई उमंग, अपार सफलता, उत्तम स्वास्थ्य और खुशहाली लेकर आए। Happy New Year!",
                'hashtags' => '#HappyNewYear #NewYear2027 #NayaSaal',
                'is_active' => true,
            ],
            [
                'title' => 'National Youth Day (Swami Vivekananda Jayanti)',
                'festival_date' => '2027-01-12',
                'category' => 'jayanti_punyatithi',
                'ready_caption' => "युवा शक्ति के प्रेरणास्रोत, महान दार्शनिक स्वामी विवेकानंद जी की जयंती 'राष्ट्रीय युवा दिवस' पर उन्हें कोटि-कोटि नमन।\n\n'उठो, जागो और तब तक मत रुको जब तक लक्ष्य की प्राप्ति न हो जाए।'",
                'hashtags' => '#NationalYouthDay #SwamiVivekananda #YuvaDiwas',
                'is_active' => true,
            ],
            [
                'title' => 'Makar Sankranti & Pongal',
                'festival_date' => '2027-01-14',
                'category' => 'religious_festival',
                'ready_caption' => "सूर्य उपासना, उल्लास एवं नव-ऊर्जा के पावन पर्व मकर संक्रांति, पोंगल एवं बिहू की आप सभी को हार्दिक बधाई एवं शुभकामनाएं।",
                'hashtags' => '#MakarSankranti #HappyPongal #Uttarayan',
                'is_active' => true,
            ],
            [
                'title' => 'Parakram Diwas (Netaji Subhas Chandra Bose Jayanti)',
                'festival_date' => '2027-01-23',
                'category' => 'jayanti_punyatithi',
                'ready_caption' => "माँ भारती के अमर सपूत, आज़ाद हिन्द फ़ौज के महानायक नेताजी सुभाष चन्द्र बोस जी की जयंती 'पराक्रम दिवस' पर उन्हें शत्-शत् नमन।\n\n'तुम मुझे खून दो, मैं तुम्हें आज़ादी दूंगा!' - जय हिन्द!",
                'hashtags' => '#ParakramDiwas #NetajiSubhasChandraBose #Netaji #JaiHind',
                'is_active' => true,
            ],
            [
                'title' => 'Republic Day',
                'festival_date' => '2027-01-26',
                'category' => 'national_day',
                'ready_caption' => "समस्त देशवासियों को 78वें गणतंत्र दिवस की हार्दिक बधाई एवं शुभकामनाएं।\n\nआइए, हम सभी मिलकर भारत के संविधान के मूल्यों की रक्षा करने तथा देश की एकता, अखंडता और संप्रभुता को अक्षुण्ण बनाए रखने का संकल्प लें। जय हिन्द! वन्दे मातरम्!",
                'hashtags' => '#RepublicDay #GantantraDiwas #26January #JaiHind #IndianConstitution',
                'is_active' => true,
            ],
            [
                'title' => 'Maha Shivratri',
                'festival_date' => '2027-03-06',
                'category' => 'religious_festival',
                'ready_caption' => "देवाधिदेव महादेव और मां पार्वती के पावन पर्व 'महाशिवरात्रि' की समस्त देशवासियों को हार्दिक शुभकामनाएं।\n\nभगवान भोलेनाथ की कृपा से आप सभी के जीवन में सुख, शांति, समृद्धि और आरोग्य की वर्षा हो। हर हर महादेव!",
                'hashtags' => '#MahaShivratri #HarHarMahadev #OmNamahShivaya',
                'is_active' => true,
            ],
            [
                'title' => 'Holi Mahotsav',
                'festival_date' => '2027-03-22',
                'category' => 'religious_festival',
                'ready_caption' => "रंगों, उल्लास और सामाजिक सौहार्द के महापर्व होली की आप सभी को हार्दिक शुभकामनाएं!\n\nयह पावन पर्व आप सभी के जीवन में खुशियों, उमंग और प्रेम के विविध रंगों का संचार करे।",
                'hashtags' => '#HappyHoli #Holi2027 #FestivalOfColours #HoliHai',
                'is_active' => true,
            ],
            [
                'title' => 'Dr. B.R. Ambedkar Jayanti',
                'festival_date' => '2027-04-14',
                'category' => 'jayanti_punyatithi',
                'ready_caption' => "भारतीय संविधान के मुख्य शिल्पकार, भारत रत्न बोधिसत्व डॉ. भीमराव अम्बेडकर जी की जयंती पर उन्हें कोटि-कोटि नमन एवं आप सभी को हार्दिक शुभकामनाएं।\n\n'शिक्षित बनो, संगठित रहो, संघर्ष करो।' - बाबासाहेब",
                'hashtags' => '#AmbedkarJayanti #Babasaheb #DrAmbedkar #JaiBhim',
                'is_active' => true,
            ],
            [
                'title' => 'Ram Navami',
                'festival_date' => '2027-04-15',
                'category' => 'religious_festival',
                'ready_caption' => "मर्यादा पुरुषोत्तम भगवान श्री राम के जन्मोत्सव 'श्री राम नवमी' के पावन पर्व की आप सभी को हार्दिक बधाई एवं शुभकामनाएं।\n\nप्रभु श्री राम की कृपा से आपके जीवन में सुख, शांति, समृद्धि और उत्तम स्वास्थ्य का वास हो। जय श्री राम!",
                'hashtags' => '#RamNavami #JaiShriRam #ShriRamNavami #Ayodhya',
                'is_active' => true,
            ],
        ];

        $bjp = \App\Models\PoliticalParty::where('short_code', 'BJP')->first();
        $inc = \App\Models\PoliticalParty::where('short_code', 'INC')->first();
        $aap = \App\Models\PoliticalParty::where('short_code', 'AAP')->first();

        $sampleTemplates = [
            ['title' => 'Heritage Floral Edition', 'image' => 'images/templates/template-1.jpg', 'party_id' => $bjp?->id],
            ['title' => 'Divine Classic Border', 'image' => 'images/templates/template-2.jpg', 'party_id' => null],
            ['title' => 'Patriotic Golden Aura', 'image' => 'images/templates/template-3.jpg', 'party_id' => $inc?->id],
            ['title' => 'National Standard Strip', 'image' => 'images/templates/template-4.jpg', 'party_id' => $bjp?->id],
            ['title' => 'Sacred Traditional Blessings', 'image' => 'images/templates/template-5.jpg', 'party_id' => null],
            ['title' => 'Memorial Tribute Frame', 'image' => 'images/templates/template-6.jpg', 'party_id' => null],
            ['title' => 'Celebration Glow Card', 'image' => 'images/templates/template-7.jpg', 'party_id' => $aap?->id],
            ['title' => 'Veer Standard Royal Saffron', 'image' => 'images/templates/template-8.jpg', 'party_id' => $bjp?->id],
        ];

        foreach ($festivals as $fest) {
            $festival = Festival::updateOrCreate(
                ['title' => $fest['title'], 'festival_date' => $fest['festival_date']],
                $fest
            );

            foreach ($sampleTemplates as $tmpl) {
                \App\Models\FestivalTemplate::updateOrCreate(
                    [
                        'festival_id' => $festival->id,
                        'title' => $tmpl['title'],
                    ],
                    [
                        'party_id' => $tmpl['party_id'],
                        'template_image_path' => $tmpl['image'],
                        'thumbnail_path' => $tmpl['image'],
                        'photo_position' => 'bottom-right',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
