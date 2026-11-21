<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_brandings', function (Blueprint $table) {
            $table->string('facebook_handle', 100)->nullable()->after('phone_number');
            $table->string('instagram_handle', 100)->nullable()->after('facebook_handle');
            $table->string('twitter_handle', 100)->nullable()->after('instagram_handle');
        });
    }

    public function down(): void
    {
        Schema::table('user_brandings', function (Blueprint $table) {
            $table->dropColumn(['facebook_handle', 'instagram_handle', 'twitter_handle']);
        });
    }
};
