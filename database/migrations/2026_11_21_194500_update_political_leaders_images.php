<?php

use App\Models\PoliticalLeader;
use App\Models\PoliticalParty;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $map = [
            'Narendra Modi' => 'images/leaders/narendramodi.jpg',
            'Amit Shah' => 'images/leaders/amitshah.jpg',
            'Yogi Adityanath' => 'images/leaders/yogiadityanath.jpg',
            'Keshav Prasad Maurya' => 'images/leaders/keshavprshadmorya.jpg',
            'Atal Bihari Vajpayee' => 'images/leaders/atalbiharibajpyee.jpg',
            'Rahul Gandhi' => 'images/leaders/rahulgandhi.jpg',
            'Arvind Kejriwal' => 'images/leaders/arvindkejriwal.jpg',
            'Late Mulayam Singh Yadav' => 'images/leaders/mulayamsinghyadav.jpg',
            'Mulayam Singh Yadav' => 'images/leaders/mulayamsinghyadav.jpg',
            'Akhilesh Yadav' => 'images/leaders/akhileshyadav.jpg',
        ];

        foreach ($map as $name => $image) {
            PoliticalLeader::where('name', $name)->update(['image' => $image]);
        }

        // Ensure Atal Bihari Vajpayee exists in BJP if not already present
        $bjp = PoliticalParty::where('short_code', 'BJP')->first();
        if ($bjp) {
            PoliticalLeader::firstOrCreate(
                ['party_id' => $bjp->id, 'name' => 'Atal Bihari Vajpayee'],
                [
                    'designation' => 'Former Prime Minister & Bharat Ratna',
                    'image' => 'images/leaders/atalbiharibajpyee.jpg',
                    'state' => 'National',
                    'is_active' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        // No down migration needed
    }
};
