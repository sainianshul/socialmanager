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
                ['name' => 'Narendra Modi', 'designation' => 'Prime Minister', 'image' => 'images/leaders/modiji.png', 'state' => 'National', 'order' => 1],
                ['name' => 'Amit Shah', 'designation' => 'Home Minister', 'image' => 'images/leaders/amitshah.jpg', 'state' => 'National', 'order' => 2],
                ['name' => 'J.P. Nadda', 'designation' => 'BJP National President', 'image' => 'images/leaders/jpnadda.jpg', 'state' => 'National', 'order' => 3],
                ['name' => 'Rajnath Singh', 'designation' => 'Defence Minister', 'image' => 'images/leaders/rajnathsingh.jpg', 'state' => 'National', 'order' => 4],
                ['name' => 'Nitin Gadkari', 'designation' => 'Union Minister, Road Transport', 'image' => 'images/leaders/nitingadkari.jpg', 'state' => 'National', 'order' => 5],
                ['name' => 'Yogi Adityanath', 'designation' => 'Chief Minister, UP', 'image' => 'images/leaders/yogiadityanath.jpg', 'state' => 'Uttar Pradesh', 'order' => 6],
                ['name' => 'Keshav Prasad Maurya', 'designation' => 'Deputy CM, UP', 'image' => 'images/leaders/keshavprshadmorya.jpg', 'state' => 'Uttar Pradesh', 'order' => 7],
                ['name' => 'Brajesh Pathak', 'designation' => 'Deputy CM, UP', 'image' => 'images/leaders/brajeshpathak.jpg', 'state' => 'Uttar Pradesh', 'order' => 8],
                ['name' => 'Nayab Singh Saini', 'designation' => 'Chief Minister, Haryana', 'image' => 'images/leaders/nayabsinghsaini.jpg', 'state' => 'Haryana', 'order' => 9],
                ['name' => 'Rekha Gupta', 'designation' => 'General Secretary, Delhi BJP', 'image' => 'images/leaders/rekhagupta.jpg', 'state' => 'Delhi', 'order' => 10],
                ['name' => 'Bhajan Lal Sharma', 'designation' => 'Chief Minister, Rajasthan', 'image' => 'images/leaders/bhajanlalsharma.jpg', 'state' => 'Rajasthan', 'order' => 11],
                ['name' => 'Mohan Yadav', 'designation' => 'Chief Minister, MP', 'image' => 'images/leaders/mohandayadav.jpg', 'state' => 'Madhya Pradesh', 'order' => 12],
                ['name' => 'Pushkar Singh Dhami', 'designation' => 'Chief Minister, Uttarakhand', 'image' => 'images/leaders/pushkarsinghdhami.jpg', 'state' => 'Uttarakhand', 'order' => 13],
                ['name' => 'Himanta Biswa Sarma', 'designation' => 'Chief Minister, Assam', 'image' => 'images/leaders/himantabiswasarma.jpg', 'state' => 'Assam', 'order' => 14],
                ['name' => 'Devendra Fadnavis', 'designation' => 'Deputy CM, Maharashtra', 'image' => 'images/leaders/devendrafadnavis.jpg', 'state' => 'Maharashtra', 'order' => 15],
                ['name' => 'Bhupendra Patel', 'designation' => 'Chief Minister, Gujarat', 'image' => 'images/leaders/bhupendrapatel.jpg', 'state' => 'Gujarat', 'order' => 16],
                ['name' => 'Vishnu Deo Sai', 'designation' => 'Chief Minister, Chhattisgarh', 'image' => 'images/leaders/vishnudeosai.jpg', 'state' => 'Chhattisgarh', 'order' => 17],
                ['name' => 'Shivraj Singh Chouhan', 'designation' => 'Union Minister, Agriculture', 'image' => 'images/leaders/shivrajsinghchouhan.jpg', 'state' => 'National', 'order' => 18],
                ['name' => 'Manohar Lal Khattar', 'designation' => 'Union Minister, Power & Housing', 'image' => 'images/leaders/manoharlalkhattar.jpg', 'state' => 'National', 'order' => 19],
                ['name' => 'Smriti Irani', 'designation' => 'Senior BJP Leader', 'image' => 'images/leaders/smritiirani.jpg', 'state' => 'National', 'order' => 20],
                ['name' => 'Anurag Thakur', 'designation' => 'Member of Parliament', 'image' => 'images/leaders/anuragthakur.jpg', 'state' => 'Himachal Pradesh', 'order' => 21],
                ['name' => 'Piyush Goyal', 'designation' => 'Union Minister, Commerce', 'image' => 'images/leaders/piyushgoyal.jpg', 'state' => 'National', 'order' => 22],
                ['name' => 'Ashwini Vaishnaw', 'designation' => 'Union Minister, Railways & IT', 'image' => 'images/leaders/ashwinivaishnaw.jpg', 'state' => 'National', 'order' => 23],
                ['name' => 'Dharmendra Pradhan', 'designation' => 'Union Minister, Education', 'image' => 'images/leaders/dharmendrapradhan.jpg', 'state' => 'National', 'order' => 24],
                ['name' => 'Kiren Rijiju', 'designation' => 'Union Minister, Parliamentary Affairs', 'image' => 'images/leaders/kirenrijiju.jpg', 'state' => 'National', 'order' => 25],
                ['name' => 'Chaudhary Bhupendra Singh', 'designation' => 'State President, BJP UP', 'image' => 'images/leaders/bhupendrasingh.jpg', 'state' => 'Uttar Pradesh', 'order' => 26],
                ['name' => 'Atal Bihari Vajpayee', 'designation' => 'Former Prime Minister & Bharat Ratna', 'image' => 'images/leaders/atalbiharibajpyee.jpg', 'state' => 'National', 'order' => 27],
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
                ['name' => 'Mallikarjun Kharge', 'designation' => 'Congress President', 'image' => 'images/leaders/mallikarjunkharge.jpg', 'state' => 'National', 'order' => 1],
                ['name' => 'Sonia Gandhi', 'designation' => 'Congress Parliamentary Leader', 'image' => 'images/leaders/soniagandhi.jpg', 'state' => 'National', 'order' => 2],
                ['name' => 'Rahul Gandhi', 'designation' => 'Leader of Opposition', 'image' => 'images/leaders/rahulgandhi.jpg', 'state' => 'National', 'order' => 3],
                ['name' => 'Priyanka Gandhi Vadra', 'designation' => 'General Secretary', 'image' => 'images/leaders/priyankagandhi.jpg', 'state' => 'National', 'order' => 4],
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
                ['name' => 'Bhagwant Mann', 'designation' => 'Chief Minister, Punjab', 'image' => 'images/leaders/bhagwantmann.jpg', 'state' => 'Punjab', 'order' => 2],
                ['name' => 'Sanjay Singh', 'designation' => 'Rajya Sabha MP', 'image' => null, 'state' => 'Delhi', 'order' => 3],
                ['name' => 'Atishi Marlena', 'designation' => 'Chief Minister, Delhi', 'image' => 'images/leaders/atishi.jpg', 'state' => 'Delhi', 'order' => 4],
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
                ['name' => 'Dimple Yadav', 'designation' => 'Lok Sabha MP', 'image' => 'images/leaders/dimpleyadav.jpg', 'state' => 'Uttar Pradesh', 'order' => 3],
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

        // 5. BSP Leaders & Default Strip
        $bsp = PoliticalParty::where('short_code', 'BSP')->first();
        if ($bsp) {
            $bspLeaders = [
                ['name' => 'Mayawati', 'designation' => 'National President & Former UP CM', 'image' => 'images/leaders/mayawati.jpg', 'state' => 'Uttar Pradesh', 'order' => 1],
                ['name' => 'Late Kanshi Ram', 'designation' => 'Founder, BSP', 'image' => null, 'state' => 'Uttar Pradesh', 'order' => 2],
            ];

            $savedBspLeaders = [];
            foreach ($bspLeaders as $data) {
                $leader = PoliticalLeader::updateOrCreate(
                    ['party_id' => $bsp->id, 'name' => $data['name']],
                    [
                        'designation' => $data['designation'],
                        'image' => $data['image'] ?? null,
                        'state' => $data['state'],
                        'is_active' => true,
                    ]
                );
                $savedBspLeaders[$data['order']] = $leader;
            }

            $bspStrip = LeaderStrip::updateOrCreate(
                ['party_id' => $bsp->id, 'is_system' => true, 'is_default' => true],
                ['name' => 'Bahujan Samaj Party Leadership']
            );

            for ($i = 1; $i <= 2; $i++) {
                if (isset($savedBspLeaders[$i])) {
                    LeaderStripItem::updateOrCreate(
                        ['leader_strip_id' => $bspStrip->id, 'political_leader_id' => $savedBspLeaders[$i]->id],
                        ['order' => $i]
                    );
                }
            }
        }
    }
}
