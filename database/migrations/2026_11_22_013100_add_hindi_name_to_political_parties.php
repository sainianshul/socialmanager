<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('political_parties', function (Blueprint $table) {
            $table->string('hindi_name')->nullable()->after('name');
        });

        // Seed Hindi names for existing parties
        \App\Models\PoliticalParty::where('short_code', 'BJP')
            ->update(['hindi_name' => 'भारतीय जनता पार्टी']);
        \App\Models\PoliticalParty::where('short_code', 'INC')
            ->update(['hindi_name' => 'भारतीय राष्ट्रीय कांग्रेस']);
        \App\Models\PoliticalParty::where('short_code', 'AAP')
            ->update(['hindi_name' => 'आम आदमी पार्टी']);
        \App\Models\PoliticalParty::where('short_code', 'BSP')
            ->update(['hindi_name' => 'बहुजन समाज पार्टी']);
        \App\Models\PoliticalParty::where('short_code', 'SP')
            ->update(['hindi_name' => 'समाजवादी पार्टी']);
        \App\Models\PoliticalParty::where('short_code', 'SHS')
            ->update(['hindi_name' => 'शिव सेना']);
        \App\Models\PoliticalParty::where('short_code', 'JDU')
            ->update(['hindi_name' => 'जनता दल (यूनाइटेड)']);
    }

    public function down(): void
    {
        Schema::table('political_parties', function (Blueprint $table) {
            $table->dropColumn('hindi_name');
        });
    }
};
