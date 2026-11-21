<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('festival_templates', function (Blueprint $table) {
            $table->string('preset_key', 60)->nullable()->default('classic_political_right')->after('photo_position');
            $table->json('layout_config')->nullable()->after('preset_key');
        });

        Schema::table('political_parties', function (Blueprint $table) {
            $table->string('group_cutout_image')->nullable()->after('top_leaders_strip');
        });
    }

    public function down(): void
    {
        Schema::table('festival_templates', function (Blueprint $table) {
            $table->dropColumn(['preset_key', 'layout_config']);
        });

        Schema::table('political_parties', function (Blueprint $table) {
            $table->dropColumn('group_cutout_image');
        });
    }
};
