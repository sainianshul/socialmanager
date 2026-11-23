<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class RestoreAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 1)->first() ?? User::first();
        if (!$admin) {
            return;
        }

        // 1. Facebook Page: Healthiiiii
        Account::updateOrCreate(
            [
                'user_id' => $admin->id,
                'account_type' => Account::TYPE_FACEBOOK,
                'meta_account_id' => '1404772002715952',
            ],
            [
                'name' => 'Healthiiiii',
                'category' => 'Health/beauty',
                'instagram_business_account_id' => '17841425196297770',
                'profile_pic_url' => 'https://scontent-hkg4-1.xx.fbcdn.net/v/t39.30808-1/876839712_122097994491532143_3469919312352779559_n.jpg?stp=c424.25.1200.1200a_cp0_dst-jpg_s50x50_tt6&_nc_cat=106&_nc_map=urlgen_bucketless&ccb=1-7&_nc_sid=f907e8&_nc_ohc=b7UGKQ2WC8IQ7kNvwEQZywZ&_nc_oc=AdoS8IMZMX7VssoraWnjSJAlXnW8-ne6D2_XFm2yAtFmvCN7DH-2tWeOtKoZKoQd-DA&_nc_zt=24&_nc_ht=scontent-hkg4-1.xx&edm=AJdBtusEAAAA&_nc_gid=_1QNsG3pCcam1QH7U2f5xg&_nc_tpa=Q5bMBQJov-wxt8uVjzA6XeQsdwQn49UrAIjA5yIsPOPMcoQ5JytLOdSlqEUNLkEUSoZybyIcMqwk5EAUMQ&oh=00_AQSPm6gWqzLCbBQahSwODtZZFBsDyTxrvapCjdbl1FQviw&oe=6B00E76C',
                'access_token' => 'EAAXkeWwXTtgBTDUJjxUgRJ71IOBj3vsmu59ZAQI4LnCsEOQ0jUZC5OJgIAS1sV5YKxzQdUygIS7SGXxHuEOsDqWEaU1ykMrSWAKQLrCnY04mTZAwZCPQNPqRSMTXuOeQRvg14hxFexFvHWLZBkZALVimxmzXvfawxpoJNhdq4UfKyQOxjZAFECfkP66NekWNBR10jWcibFT',
                'is_active' => true,
                'webhook_active' => true,
                'ai_tone' => 'Humble & Respectful',
                'ai_language' => 'Hindi + Hinglish',
                'auto_reply_mode' => 1,
            ]
        );

        // 2. Instagram Business Account: Nishant Jha (nishantjhaa007)
        Account::updateOrCreate(
            [
                'user_id' => $admin->id,
                'account_type' => Account::TYPE_INSTAGRAM,
                'meta_account_id' => '17841425196297770',
            ],
            [
                'name' => 'Nishant Jha',
                'username' => 'nishantjhaa007',
                'category' => 'Public Figure',
                'profile_pic_url' => 'https://scontent-hkg4-1.xx.fbcdn.net/v/t51.82787-15/883626839_18122320366839877_8843536929589353275_n.jpg?_nc_cat=103&_nc_map=urlgen_bucketless&ccb=1-7&_nc_sid=7d201b&_nc_ohc=TZ1QriOsfWIQ7kNvwHfQetb&_nc_oc=Adqgas_nze3wjcN5B299ySoWiBNC0rT6Jqh5nm-5n4_IohpSPRThoKSULf_aO5B_WlI&_nc_zt=23&_nc_ht=scontent-hkg4-1.xx&edm=AJdBtusEAAAA&_nc_gid=_1QNsG3pCcam1QH7U2f5xg&oh=00_AQRJ5jg0KT4eRlfLQsNdpvonD3nKHoblQ1BzezWiNAFfDw&oe=6B00E791',
                'access_token' => 'EAAXkeWwXTtgBTDUJjxUgRJ71IOBj3vsmu59ZAQI4LnCsEOQ0jUZC5OJgIAS1sV5YKxzQdUygIS7SGXxHuEOsDqWEaU1ykMrSWAKQLrCnY04mTZAwZCPQNPqRSMTXuOeQRvg14hxFexFvHWLZBkZALVimxmzXvfawxpoJNhdq4UfKyQOxjZAFECfkP66NekWNBR10jWcibFT',
                'is_active' => true,
                'webhook_active' => true,
                'ai_tone' => 'Humble & Respectful',
                'ai_language' => 'Hindi + Hinglish',
                'auto_reply_mode' => 1,
            ]
        );

        // 3. YouTube Channel: Anshul Saini
        Account::updateOrCreate(
            [
                'user_id' => $admin->id,
                'account_type' => Account::TYPE_YOUTUBE,
                'name' => 'Anshul Saini',
            ],
            [
                'username' => 'Anshul Saini',
                'category' => 'YouTube Channel',
                'access_token' => 'restored-token',
                'is_active' => true,
                'ai_tone' => 'Humble & Respectful',
                'ai_language' => 'Hindi + Hinglish',
                'auto_reply_mode' => 1,
            ]
        );
    }
}
