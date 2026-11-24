<?php

namespace Database\Seeders;

use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\PoliticalParty;
use Illuminate\Database\Seeder;

class FestivalSeeder extends Seeder
{
    public function run(): void
    {
        $bjp = PoliticalParty::where('short_code', 'BJP')->first();
        $inc = PoliticalParty::where('short_code', 'INC')->first();
        $aap = PoliticalParty::where('short_code', 'AAP')->first();

        $festivals = [
            // ─── NOVEMBER FESTIVALS & SPECIAL DAYS (REAL TEMPLATES) ─────────────
            [
                'title' => 'National Legal Services Day',
                'festival_date' => '2026-11-09',
                'category' => 'national_day',
                'ready_caption' => "राष्ट्रीय कानूनी सेवा दिवस पर सभी विधिक कर्मियों एवं देशवासियों को हार्दिक शुभकामनाएं।\n\nदेश के हर कमजोर, वंचित और जरूरतमंद नागरिक तक सुलभ, त्वरित एवं मुफ्त कानूनी सहायता पहुँचाने का संकल्प लें।",
                'hashtags' => '#NationalLegalServicesDay #LegalServicesDay #NyaySabkeLiye #JusticeForAll',
                'is_active' => true,
                'primary_template' => 'images/templates/national-legal-services-day.jpg',
                'primary_title' => 'National Legal Services Day Special',
            ],
            [
                'title' => 'Diwali - Deepawali Mahaparv',
                'festival_date' => '2026-11-12',
                'category' => 'religious_festival',
                'ready_caption' => "जीवन में नव ऊर्जा का संचार करने वाले पावन पर्व दीपावली की आप सभी को हार्दिक शुभकामनाएं!\n\nमां लक्ष्मी और भगवान श्री गणेश जी की कृपा से आपके घर-परिवार में सुख, समृद्धि, ऐश्वर्य और शांति का वास हो। शुभ दीपावली!",
                'hashtags' => '#HappyDiwali #Deepawali #Diwali #FestivalOfLights #SubhDeepavali',
                'is_active' => true,
                'primary_template' => 'images/templates/diwali-deepawali.jpg',
                'primary_title' => 'Deepawali Grand Celebration Edition',
            ],
            [
                'title' => 'Govardhan Puja',
                'festival_date' => '2026-11-13',
                'category' => 'religious_festival',
                'ready_caption' => "गोवर्धन धराधार गोकुल त्राणकारक।\nविष्णुबाहु कृतोच्छ्राय गवां कोटिप्रभो भव॥\n\nप्रकृति संरक्षण, गौ संवर्धन एवं भगवान श्री कृष्ण की कृपा के प्रतीक पावन पर्व 'गोवर्धन पूजा' की आप सभी को हार्दिक मंगलकामनाएं!",
                'hashtags' => '#GovardhanPuja #ShriKrishna #Annakut #GovardhanPuja2026',
                'is_active' => true,
                'primary_template' => 'images/templates/govardhan-puja.jpg',
                'primary_title' => 'Govardhan Puja Sacred Devotion Edition',
            ],
            [
                'title' => 'Bhai Dooj',
                'festival_date' => '2026-11-14',
                'category' => 'religious_festival',
                'ready_caption' => "भाई-बहन के असीम स्नेह, विश्वास और अटूट पवित्र रिश्ते के पावन पर्व 'भाई दूज' की समस्त देशवासियों को हार्दिक बधाई एवं शुभकामनाएं।",
                'hashtags' => '#BhaiDooj #BhaiDooj2026 #BhaiTika #SiblingBond',
                'is_active' => true,
                'primary_template' => 'images/templates/bhai-dooj.jpg',
                'primary_title' => 'Bhai Dooj Traditional Family Special',
            ],
            [
                'title' => 'Bal Diwas (Children Day) & Nehru Jayanti',
                'festival_date' => '2026-11-14',
                'category' => 'jayanti_punyatithi',
                'ready_caption' => "भारत के प्रथम प्रधानमंत्री पंडित जवाहरलाल नेहरू जी की जयंती पर सादर नमन एवं समस्त बच्चों को बाल दिवस की हार्दिक शुभकामनाएं!\n\nबच्चे देश का भविष्य हैं, आइए उनके उज्ज्वल और सुरक्षित भविष्य का निर्माण करें।",
                'hashtags' => '#ChildrensDay #BalDiwas #NehruJayanti #HappyChildrensDay',
                'is_active' => true,
                'primary_template' => 'images/templates/childrens-day-nehru-jayanti.jpg',
                'primary_title' => 'Children Day & Nehru Jayanti Tribute',
            ],
            [
                'title' => 'Janjatiya Gaurav Diwas (Bhagwan Birsa Munda Jayanti)',
                'festival_date' => '2026-11-15',
                'category' => 'jayanti_punyatithi',
                'ready_caption' => "महान स्वतंत्रता सेनानी, जनजातीय अस्मिता के रक्षक एवं जननायक भगवान बिरसा मुंडा जी की जयंती 'जनजातीय गौरव दिवस' पर उन्हें कोटि-कोटि प्रणाम।\n\nउलगुलान के महानायक का अदम्य साहस राष्ट्र को सदैव प्रेरित करता रहेगा।",
                'hashtags' => '#JanjatiyaGauravDiwas #BirsaMunda #BhagwanBirsaMunda #PrideOfTribalIndia',
                'is_active' => true,
                'primary_template' => 'images/templates/janjatiya-gaurav-diwas-birsa-munda.jpg',
                'primary_title' => 'Janjatiya Gaurav Diwas Birsa Munda Special',
            ],
            [
                'title' => 'Jharkhand Sthapana Diwas',
                'festival_date' => '2026-11-15',
                'category' => 'national_day',
                'ready_caption' => "प्राकृतिक संपदा, समृद्ध संस्कृति और शौर्य की वीर भूमि झारखंड के स्थापना दिवस पर समस्त प्रदेशवासियों को हार्दिक शुभकामनाएं।\n\nझारखंड निरंतर प्रगति और समृद्धि के नए शिखर छुए, यही कामना है।",
                'hashtags' => '#JharkhandFoundationDay #JharkhandSthapanaDiwas #JoharJharkhand',
                'is_active' => true,
                'primary_template' => 'images/templates/jharkhand-foundation-day.jpg',
                'primary_title' => 'Jharkhand Foundation Day Edition',
            ],
            [
                'title' => 'Rashtriya Patrakarita Diwas (National Press Day)',
                'festival_date' => '2026-11-16',
                'category' => 'national_day',
                'ready_caption' => "लोकतंत्र के चौथे स्तंभ, राष्ट्र निर्माण में समर्पित सभी पत्रकार बंधुओं को 'राष्ट्रीय पत्रकारिता दिवस' के अवसर पर हार्दिक बधाई एवं शुभकामनाएं।\n\nआपकी निर्भीक एवं निष्पक्ष लेखनी सदैव जनहित का मार्ग प्रशस्त करे।",
                'hashtags' => '#NationalPressDay #NationalPressDay2026 #MediaEthics #FourthPillar',
                'is_active' => true,
                'primary_template' => 'images/templates/national-press-day.jpg',
                'primary_title' => 'National Press Day Special Tribute',
            ],
            [
                'title' => 'Chhath Puja Mahaparv',
                'festival_date' => '2026-11-18',
                'category' => 'religious_festival',
                'ready_caption' => "लोक आस्था, पवित्रता, सूर्य उपासना एवं प्रकृति प्रेम के महापर्व 'छठ पूजा' की आप सभी को और आपके पूरे परिवार को हार्दिक शुभकामनाएं।\n\nभगवान भास्कर और छठी मइया की कृपा से आपके जीवन में सुख, शांति, समृद्धि और आरोग्यता बनी रहे। जय छठी मइया!",
                'hashtags' => '#ChhathPuja #HappyChhathPuja #ChhathMahaparv #ChhathiMaiya #SuryaPuja',
                'is_active' => true,
                'primary_template' => 'images/templates/chhath-puja.jpg',
                'primary_title' => 'Chhath Puja Mahaparv Divine Frame',
            ],
            [
                'title' => 'Flying Sikh Shri Milkha Singh Ji Jayanti',
                'festival_date' => '2026-11-20',
                'category' => 'jayanti_punyatithi',
                'ready_caption' => "एथलेटिक्स में भारत का नाम पूरे विश्व में गौरवान्वित करने वाले महान धावक 'फ्लाइंग सिख' पद्मश्री मिल्खा सिंह जी की जयंती पर उन्हें सादर नमन।\n\nआपका अदम्य साहस और दृढ़ संकल्प देश के करोड़ों युवाओं को सदैव प्रेरित करता रहेगा।",
                'hashtags' => '#FlyingSikh #MilkhaSingh #MilkhaSinghJayanti #PrideOfIndia',
                'is_active' => true,
                'primary_template' => 'images/templates/milkha-singh-jayanti.jpg',
                'primary_title' => 'Milkha Singh Ji Memorial Tribute',
            ],
            [
                'title' => 'Tulsi Vivah Mahotsav',
                'festival_date' => '2026-11-21',
                'category' => 'religious_festival',
                'ready_caption' => "मां तुलसी के पावन विवाह (तुलसी विवाह) के शुभ अवसर पर आप सभी को हार्दिक बधाई एवं अनंत शुभकामनाएं।\n\nमां तुलसी आपके जीवन को सुख, शांति, समृद्धि और उत्तम स्वास्थ्य से परिपूर्ण करें। जय श्री कृष्णा!",
                'hashtags' => '#TulsiVivah #ShubhTulsiVivah #TulsiPuja #JaiShriKrishna',
                'is_active' => true,
                'primary_template' => 'images/templates/tulsi-vivah.jpg',
                'primary_title' => 'Tulsi Vivah Divine Special',
            ],
            [
                'title' => 'Shrimati Anandiben Patel Ji Birthday',
                'festival_date' => '2026-11-21',
                'category' => 'jayanti_punyatithi',
                'ready_caption' => "गुजरात की पूर्व मुख्यमंत्री और उत्तर प्रदेश की माननीय राज्यपाल श्रीमती आनंदीबेन पटेल जी को जन्मदिवस की हार्दिक शुभकामनाएं।\n\nईश्वर से आपके उत्तम स्वास्थ्य, सुदीर्घ एवं यशस्वी जीवन की मंगल कामना करता हूँ।",
                'hashtags' => '#AnandibenPatel #GovernorUP #HappyBirthday #JanamdinKiShubhkaamnaye',
                'is_active' => true,
                'primary_template' => 'images/templates/anandiben-patel-jayanti.jpg',
                'primary_title' => 'Anandiben Patel Ji Birthday Edition',
            ],
            [
                'title' => 'Bhagwan Shri Khatu Shyam Ji Janmotsav',
                'festival_date' => '2026-11-23',
                'category' => 'religious_festival',
                'ready_caption' => "हारे का सहारा, बाबा खाटू श्याम हमारा!\n\nशीश के दानी, कलयुग के अवतारी भगवान श्री खाटू श्याम जी के जन्मोत्सव की सभी श्याम भक्तों को सहृदय शुभकामनाएं। बाबा श्याम की कृपा आप सभी पर सदैव बनी रहे। जय श्री श्याम!",
                'hashtags' => '#KhatuShyamJi #JaiShriShyam #KhatuShyamJanmotsav #HareKaSahara',
                'is_active' => true,
                'primary_template' => 'images/templates/khatu-shyam-janmotsav.jpg',
                'primary_title' => 'Khatu Shyam Ji Janmotsav Grand Edition',
                'preset_key' => 'leader_tribute_royal',
            ],
            [
                'title' => 'Devotthan Ekadashi',
                'festival_date' => '2026-11-24',
                'category' => 'religious_festival',
                'ready_caption' => "समस्त देशवासियों को देवोत्थान (प्रबोधिनी) एकादशी के पावन पर्व की हार्दिक शुभकामनाएं।\n\nइस पावन अवसर पर भगवान श्री हरि विष्णु जी की कृपा आप सभी पर सदैव बनी रहे, यही मंगल कामना है। ॐ नमो भगवते वासुदेवाय!",
                'hashtags' => '#DevotthanEkadashi #PrabodhiniEkadashi #VishnuPuja #Ekadashi',
                'is_active' => true,
                'primary_template' => 'images/templates/devotthan-ekadashi.jpg',
                'primary_title' => 'Devotthan Ekadashi Sacred Frame',
                'preset_key' => 'classic_political_right',
            ],
            [
                'title' => 'Gopashtami (Gau Pujan)',
                'festival_date' => '2026-11-25',
                'category' => 'religious_festival',
                'ready_caption' => "गौ-पूजन के पावन पर्व 'गोपाष्टमी' की आप सभी को हार्दिक शुभकामनाएं।\n\nगाय केवल एक पशु नहीं, बल्कि सनातन संस्कृति में मां स्वरूपा और समस्त देवी-देवताओं का निवास स्थान हैं। आइए, गौ सेवा और गौ संवर्धन का संकल्प लें। जय गोमाता!",
                'hashtags' => '#Gopashtami #GauPujan #GauSeva #JaiGauMata',
                'is_active' => true,
                'primary_template' => 'images/templates/gopashtami.jpg',
                'primary_title' => 'Gopashtami Gau Pujan Royal Edition',
                'preset_key' => 'classic_political_right',
                'additional_templates' => [
                    [
                        'template_image_path' => 'images/templates/gopashtami-vrindavan-divine.jpg',
                        'title' => 'Gopashtami Vrindavan Divine Edition',
                        'thumbnail_path' => 'images/templates/gopashtami-vrindavan-divine.jpg',
                        'preset_key' => 'classic_political_right',
                    ],
                ],
            ],
            [
                'title' => 'Samvidhan Diwas (Constitution Day)',
                'festival_date' => '2026-11-26',
                'category' => 'national_day',
                'ready_caption' => "समस्त देशवासियों को संविधान दिवस की हार्दिक शुभकामनाएं!\n\nविश्व के सबसे बड़े और जीवंत लोकतंत्र के आधार स्तंभ भारत के संविधान के शिल्पकारों को कोटि-कोटि नमन। आइए हम अपने मौलिक कर्तव्यों के पालन का संकल्प लें। जय हिन्द!",
                'hashtags' => '#ConstitutionDay #SamvidhanDiwas #IndianConstitution #JaiHind',
                'is_active' => true,
                'primary_template' => 'images/templates/samvidhan-diwas.jpg',
                'primary_title' => 'Samvidhan Diwas National Tribute',
                'preset_key' => 'clean_no_leaders',
            ],
            [
                'title' => 'Guru Nanak Jayanti (Prakash Parv)',
                'festival_date' => '2026-11-27',
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
                'title' => 'Republic Day',
                'festival_date' => '2027-01-26',
                'category' => 'national_day',
                'ready_caption' => "समस्त देशवासियों को 78वें गणतंत्र दिवस की हार्दिक बधाई एवं शुभकामनाएं। जय हिन्द! वन्दे मातरम्!",
                'hashtags' => '#RepublicDay #GantantraDiwas #26January #JaiHind',
                'is_active' => true,
            ],
        ];

        // Delete all old blurry dummy cropped templates
        FestivalTemplate::where('template_image_path', 'like', '%template-%')->delete();

        foreach ($festivals as $fest) {
            $primaryTemplate = $fest['primary_template'] ?? null;
            $primaryTitle = $fest['primary_title'] ?? null;
            $presetKey = $fest['preset_key'] ?? 'classic_political_right';

            $additionalTemplates = $fest['additional_templates'] ?? [];
            unset($fest['primary_template'], $fest['primary_title'], $fest['preset_key'], $fest['additional_templates']);

            $festival = Festival::updateOrCreate(
                ['title' => $fest['title'], 'festival_date' => $fest['festival_date']],
                $fest
            );

            // Only add authentic, clean uploaded posters
            if ($primaryTemplate) {
                FestivalTemplate::updateOrCreate(
                    [
                        'festival_id' => $festival->id,
                        'template_image_path' => $primaryTemplate,
                    ],
                    [
                        'party_id' => $bjp?->id,
                        'title' => $primaryTitle ?: ($festival->title . ' Authentic Special'),
                        'thumbnail_path' => $primaryTemplate,
                        'photo_position' => 'bottom-right',
                        'preset_key' => $presetKey,
                        'is_active' => true,
                    ]
                );
            }

            if (!empty($additionalTemplates)) {
                foreach ($additionalTemplates as $addTpl) {
                    FestivalTemplate::updateOrCreate(
                        [
                            'festival_id' => $festival->id,
                            'template_image_path' => $addTpl['template_image_path'],
                        ],
                        [
                            'party_id' => $bjp?->id,
                            'title' => $addTpl['title'],
                            'thumbnail_path' => $addTpl['thumbnail_path'] ?? $addTpl['template_image_path'],
                            'photo_position' => $addTpl['photo_position'] ?? 'bottom-right',
                            'preset_key' => $addTpl['preset_key'] ?? $presetKey,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
