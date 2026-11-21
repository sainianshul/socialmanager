<?php

use App\Models\PoliticalParty;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $parties = [
            [
                'name' => 'Bharatiya Janata Party',
                'short_code' => 'BJP',
                'symbol_logo' => 'images/parties/bjp.jpeg',
                'primary_color' => '#FF9933',
                'secondary_color' => '#0d6832',
                'is_active' => true,
            ],
            [
                'name' => 'Indian National Congress',
                'short_code' => 'INC',
                'symbol_logo' => 'images/parties/inc.jpeg',
                'primary_color' => '#138808',
                'secondary_color' => '#000080',
                'is_active' => true,
            ],
            [
                'name' => 'Aam Aadmi Party',
                'short_code' => 'AAP',
                'symbol_logo' => 'images/parties/aap.jpeg',
                'primary_color' => '#006699',
                'secondary_color' => '#FFCC00',
                'is_active' => true,
            ],
            [
                'name' => 'Bahujan Samaj Party',
                'short_code' => 'BSP',
                'symbol_logo' => 'images/parties/bsp.jpeg',
                'primary_color' => '#22409A',
                'secondary_color' => '#FFFFFF',
                'is_active' => true,
            ],
            [
                'name' => 'All India Trinamool Congress',
                'short_code' => 'AITC',
                'symbol_logo' => 'images/parties/aitc.jpeg',
                'primary_color' => '#207238',
                'secondary_color' => '#3C8D2F',
                'is_active' => true,
            ],
            [
                'name' => 'Communist Party of India',
                'short_code' => 'CPI',
                'symbol_logo' => 'images/parties/cpi.jpeg',
                'primary_color' => '#D62728',
                'secondary_color' => '#B2182B',
                'is_active' => true,
            ],
            [
                'name' => 'Janata Dal (United)',
                'short_code' => 'JDU',
                'symbol_logo' => 'images/parties/jdu.jpeg',
                'primary_color' => '#138808',
                'secondary_color' => '#FFFFFF',
                'is_active' => true,
            ],
            [
                'name' => 'Shiv Sena',
                'short_code' => 'SHS',
                'symbol_logo' => 'images/parties/ss.jpeg',
                'primary_color' => '#F37023',
                'secondary_color' => '#D14124',
                'is_active' => true,
            ],
            [
                'name' => 'Samajwadi Party',
                'short_code' => 'SP',
                'symbol_logo' => 'images/parties/sp.jpeg',
                'primary_color' => '#E21B23',
                'secondary_color' => '#138808',
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

    public function down(): void
    {
        // Keep parties intact
    }
};
