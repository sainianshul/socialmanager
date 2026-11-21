<?php

namespace Database\Seeders;

use App\Models\LeaderStrip;
use App\Models\LeaderStripItem;
use App\Models\PoliticalLeader;
use App\Models\PoliticalParty;
use Illuminate\Database\Seeder;

class PoliticalLeaderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BJP Leaders & Default Strip
        $bjp = PoliticalParty::where('short_code', 'BJP')->first();
        if ($bjp) {
            $bjpLeaders = [
                ['name' => 'Narendra Modi', 'designation' => 'Prime Minister', 'image' => 'images/leaders/narendramodi.jpg', 'state' => 'National', 'order' => 1],
                ['name' => 'Amit Shah', 'designation' => 'Home Minister', 'image' => 'images/leaders/amitshah.jpg', 'state' => 'National', 'order' => 2],
                ['name' => 'J.P. Nadda', 'designation' => 'BJP National President', 'image' => null, 'state' => 'National', 'order' => 3],
                ['name' => 'Rajnath Singh', 'designation' => 'Defence Minister', 'image' => null, 'state' => 'National', 'order' => 4],
                ['name' => 'Yogi Adityanath', 'designation' => 'Chief Minister, UP', 'image' => 'images/leaders/yogiadityanath.jpg', 'state' => 'Uttar Pradesh', 'order' => 5],
                ['name' => 'Keshav Prasad Maurya', 'designation' => 'Deputy CM, UP', 'image' => 'images/leaders/keshavprshadmorya.jpg', 'state' => 'Uttar Pradesh', 'order' => 6],
                ['name' => 'Brajesh Pathak', 'designation' => 'Deputy CM, UP', 'image' => null, 'state' => 'Uttar Pradesh', 'order' => 7],
                ['name' => 'Atal Bihari Vajpayee', 'designation' => 'Former Prime Minister & Bharat Ratna', 'image' => 'images/leaders/atalbiharibajpyee.jpg', 'state' => 'National', 'order' => 8],
                ['name' => 'Bhajan Lal Sharma', 'designation' => 'Chief Minister, Rajasthan', 'image' => null, 'state' => 'Rajasthan', 'order' => 9],
                ['name' => 'Mohan Yadav', 'designation' => 'Chief Minister, MP', 'image' => null, 'state' => 'Madhya Pradesh', 'order' => 10],
            ];

            $savedBjpLeaders = [];
            foreach ($bjpLeaders as $data) {
                $leader = PoliticalLeader::updateOrCreate(
                    ['party_id' => $bjp->id, 'name' => $data['name']],
                    [
                        'designation' => $data['designation'],
                        'image' => $data['image'] ?? null,
                        'state' => $data['state'],
                        'is_active' => true,
                    ]
                );
                $savedBjpLeaders[$data['order']] = $leader;
            }

            // Create BJP System Default Strip (UP & Central Leadership)
            $bjpStrip = LeaderStrip::updateOrCreate(
                ['party_id' => $bjp->id, 'is_system' => true, 'is_default' => true],
                ['name' => 'BJP Uttar Pradesh & Central Leadership']
            );

            // Attach top 7 leaders in sequence
            for ($i = 1; $i <= 7; $i++) {
                if (isset($savedBjpLeaders[$i])) {
                    LeaderStripItem::updateOrCreate(
                        ['leader_strip_id' => $bjpStrip->id, 'political_leader_id' => $savedBjpLeaders[$i]->id],
                        ['order' => $i]
                    );
                }
            }
        }

        // 2. INC Leaders & Default Strip
        $inc = PoliticalParty::where('short_code', 'INC')->first();
        if ($inc) {
            $incLeaders = [
                ['name' => 'Mallikarjun Kharge', 'designation' => 'Congress President', 'image' => null, 'state' => 'National', 'order' => 1],
                ['name' => 'Sonia Gandhi', 'designation' => 'Congress Parliamentary Leader', 'image' => null, 'state' => 'National', 'order' => 2],
                ['name' => 'Rahul Gandhi', 'designation' => 'Leader of Opposition', 'image' => 'images/leaders/rahulgandhi.jpg', 'state' => 'National', 'order' => 3],
                ['name' => 'Priyanka Gandhi Vadra', 'designation' => 'General Secretary', 'image' => null, 'state' => 'National', 'order' => 4],
                ['name' => 'Sukhvinder Singh Sukhu', 'designation' => 'Chief Minister, HP', 'image' => null, 'state' => 'Himachal Pradesh', 'order' => 5],
            ];

            $savedIncLeaders = [];
            foreach ($incLeaders as $data) {
                $leader = PoliticalLeader::updateOrCreate(
                    ['party_id' => $inc->id, 'name' => $data['name']],
                    [
                        'designation' => $data['designation'],
                        'image' => $data['image'] ?? null,
                        'state' => $data['state'],
                        'is_active' => true,
                    ]
                );
                $savedIncLeaders[$data['order']] = $leader;
            }

            $incStrip = LeaderStrip::updateOrCreate(
                ['party_id' => $inc->id, 'is_system' => true, 'is_default' => true],
                ['name' => 'Congress Central Leadership']
            );

            for ($i = 1; $i <= 4; $i++) {
                if (isset($savedIncLeaders[$i])) {
                    LeaderStripItem::updateOrCreate(
                        ['leader_strip_id' => $incStrip->id, 'political_leader_id' => $savedIncLeaders[$i]->id],
                        ['order' => $i]
                    );
                }
            }
        }

        // 3. AAP Leaders & Default Strip
        $aap = PoliticalParty::where('short_code', 'AAP')->first();
        if ($aap) {
            $aapLeaders = [
                ['name' => 'Arvind Kejriwal', 'designation' => 'National Convener', 'image' => 'images/leaders/arvindkejriwal.jpg', 'state' => 'Delhi', 'order' => 1],
                ['name' => 'Bhagwant Mann', 'designation' => 'Chief Minister, Punjab', 'image' => null, 'state' => 'Punjab', 'order' => 2],
                ['name' => 'Sanjay Singh', 'designation' => 'Rajya Sabha MP', 'image' => null, 'state' => 'Delhi', 'order' => 3],
                ['name' => 'Atishi Marlena', 'designation' => 'Chief Minister, Delhi', 'image' => null, 'state' => 'Delhi', 'order' => 4],
            ];

            $savedAapLeaders = [];
            foreach ($aapLeaders as $data) {
                $leader = PoliticalLeader::updateOrCreate(
                    ['party_id' => $aap->id, 'name' => $data['name']],
                    [
                        'designation' => $data['designation'],
                        'image' => $data['image'] ?? null,
                        'state' => $data['state'],
                        'is_active' => true,
                    ]
                );
                $savedAapLeaders[$data['order']] = $leader;
            }

            $aapStrip = LeaderStrip::updateOrCreate(
                ['party_id' => $aap->id, 'is_system' => true, 'is_default' => true],
                ['name' => 'AAP Leadership Team']
            );

            for ($i = 1; $i <= 4; $i++) {
                if (isset($savedAapLeaders[$i])) {
                    LeaderStripItem::updateOrCreate(
                        ['leader_strip_id' => $aapStrip->id, 'political_leader_id' => $savedAapLeaders[$i]->id],
                        ['order' => $i]
                    );
                }
            }
        }

        // 4. SP Leaders & Default Strip
        $sp = PoliticalParty::where('short_code', 'SP')->first();
        if ($sp) {
            $spLeaders = [
                ['name' => 'Late Mulayam Singh Yadav', 'designation' => 'Founder & Netaji', 'image' => 'images/leaders/mulayamsinghyadav.jpg', 'state' => 'Uttar Pradesh', 'order' => 1],
                ['name' => 'Akhilesh Yadav', 'designation' => 'National President', 'image' => 'images/leaders/akhileshyadav.jpg', 'state' => 'Uttar Pradesh', 'order' => 2],
                ['name' => 'Dimple Yadav', 'designation' => 'Lok Sabha MP', 'image' => null, 'state' => 'Uttar Pradesh', 'order' => 3],
                ['name' => 'Shivpal Singh Yadav', 'designation' => 'General Secretary', 'image' => null, 'state' => 'Uttar Pradesh', 'order' => 4],
            ];

            $savedSpLeaders = [];
            foreach ($spLeaders as $data) {
                $leader = PoliticalLeader::updateOrCreate(
                    ['party_id' => $sp->id, 'name' => $data['name']],
                    [
                        'designation' => $data['designation'],
                        'image' => $data['image'] ?? null,
                        'state' => $data['state'],
                        'is_active' => true,
                    ]
                );
                $savedSpLeaders[$data['order']] = $leader;
            }

            $spStrip = LeaderStrip::updateOrCreate(
                ['party_id' => $sp->id, 'is_system' => true, 'is_default' => true],
                ['name' => 'Samajwadi Party Leadership']
            );

            for ($i = 1; $i <= 4; $i++) {
                if (isset($savedSpLeaders[$i])) {
                    LeaderStripItem::updateOrCreate(
                        ['leader_strip_id' => $spStrip->id, 'political_leader_id' => $savedSpLeaders[$i]->id],
                        ['order' => $i]
                    );
                }
            }
        }
    }
}
