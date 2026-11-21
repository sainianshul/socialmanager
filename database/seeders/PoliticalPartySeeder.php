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
                'short_code' => 'BJP',
                'symbol_logo' => 'parties/bjp_lotus.png',
                'top_leaders_strip' => 'parties/bjp_leaders_strip.png',
                'primary_color' => '#FF9933', // Saffron
                'secondary_color' => '#138808', // Green
                'is_active' => true,
            ],
            [
                'name' => 'Indian National Congress',
                'short_code' => 'INC',
                'symbol_logo' => 'parties/inc_hand.png',
                'top_leaders_strip' => 'parties/inc_leaders_strip.png',
                'primary_color' => '#138808', // Green
                'secondary_color' => '#000080', // Blue
                'is_active' => true,
            ],
            [
                'name' => 'Aam Aadmi Party',
                'short_code' => 'AAP',
                'symbol_logo' => 'parties/aap_broom.png',
                'top_leaders_strip' => 'parties/aap_leaders_strip.png',
                'primary_color' => '#006699', // Cyan / Blue
                'secondary_color' => '#FFCC00', // Yellow
                'is_active' => true,
            ],
            [
                'name' => 'Samajwadi Party',
                'short_code' => 'SP',
                'symbol_logo' => 'parties/sp_cycle.png',
                'top_leaders_strip' => 'parties/sp_leaders_strip.png',
                'primary_color' => '#E21B23', // Red
                'secondary_color' => '#138808', // Green
                'is_active' => true,
            ],
            [
                'name' => 'Bahujan Samaj Party',
                'short_code' => 'BSP',
                'symbol_logo' => 'parties/bsp_elephant.png',
                'top_leaders_strip' => 'parties/bsp_leaders_strip.png',
                'primary_color' => '#22409A', // Blue
                'secondary_color' => '#FFFFFF', // White
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
