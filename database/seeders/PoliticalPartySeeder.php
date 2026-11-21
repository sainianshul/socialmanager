<?php

namespace Database\Seeders;

use App\Models\PoliticalParty;
use Illuminate\Database\Seeder;

class PoliticalPartySeeder extends Seeder
{
    public function run(): void
    {
        $parties = [
            [
                'name' => 'Bharatiya Janata Party',
                'hindi_name' => 'भारतीय जनता पार्टी',
                'short_code' => 'BJP',
                'symbol_logo' => 'images/parties/bjp.jpeg',
                'top_leaders_strip' => null,
                'group_cutout_image' => 'images/parties/bjp-group-cutout.png',
                'primary_color' => '#FF9933', // Saffron
                'secondary_color' => '#0d6832', // Green
                'is_active' => true,
            ],
            [
                'name' => 'Indian National Congress',
                'hindi_name' => 'भारतीय राष्ट्रीय कांग्रेस',
                'short_code' => 'INC',
                'symbol_logo' => 'images/parties/inc.jpeg',
                'top_leaders_strip' => null,
                'primary_color' => '#138808', // Green
                'secondary_color' => '#000080', // Blue
                'is_active' => true,
            ],
            [
                'name' => 'Aam Aadmi Party',
                'hindi_name' => 'आम आदमी पार्टी',
                'short_code' => 'AAP',
                'symbol_logo' => 'images/parties/aap.jpeg',
                'top_leaders_strip' => null,
                'primary_color' => '#006699', // Cyan / Blue
                'secondary_color' => '#FFCC00', // Yellow
                'is_active' => true,
            ],
            [
                'name' => 'Bahujan Samaj Party',
                'hindi_name' => 'बहुजन समाज पार्टी',
                'short_code' => 'BSP',
                'symbol_logo' => 'images/parties/bsp.jpeg',
                'top_leaders_strip' => null,
                'primary_color' => '#22409A', // Blue
                'secondary_color' => '#FFFFFF', // White
                'is_active' => true,
            ],
            [
                'name' => 'All India Trinamool Congress',
                'hindi_name' => 'ऑल इंडिया तृणमूल कांग्रेस',
                'short_code' => 'AITC',
                'symbol_logo' => 'images/parties/aitc.jpeg',
                'top_leaders_strip' => null,
                'primary_color' => '#207238', // Green
                'secondary_color' => '#3C8D2F',
                'is_active' => true,
            ],
            [
                'name' => 'Communist Party of India',
                'hindi_name' => 'भारतीय कम्युनिस्ट पार्टी',
                'short_code' => 'CPI',
                'symbol_logo' => 'images/parties/cpi.jpeg',
                'top_leaders_strip' => null,
                'primary_color' => '#D62728', // Red
                'secondary_color' => '#B2182B',
                'is_active' => true,
            ],
            [
                'name' => 'Janata Dal (United)',
                'hindi_name' => 'जनता दल (यूनाइटेड)',
                'short_code' => 'JDU',
                'symbol_logo' => 'images/parties/jdu.jpeg',
                'top_leaders_strip' => null,
                'primary_color' => '#138808', // Green
                'secondary_color' => '#FFFFFF',
                'is_active' => true,
            ],
            [
                'name' => 'Shiv Sena',
                'hindi_name' => 'शिव सेना',
                'short_code' => 'SHS',
                'symbol_logo' => 'images/parties/ss.jpeg',
                'top_leaders_strip' => null,
                'primary_color' => '#F37023', // Saffron
                'secondary_color' => '#D14124',
                'is_active' => true,
            ],
            [
                'name' => 'Samajwadi Party',
                'hindi_name' => 'समाजवादी पार्टी',
                'short_code' => 'SP',
                'symbol_logo' => 'images/parties/sp.jpeg',
                'top_leaders_strip' => null,
                'primary_color' => '#E21B23', // Red
                'secondary_color' => '#138808', // Green
                'is_active' => true,
            ],
        ];

        foreach ($parties as $party) {
            PoliticalParty::updateOrCreate(
                ['short_code' => $party['short_code']],
                $party
            );
        }
    }
}
