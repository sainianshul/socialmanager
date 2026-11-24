<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->unsignedSmallInteger('consecutive_empty_syncs')->default(0)->after('sync_interval_minutes')->comment('Counter of consecutive syncs with 0 new comments for exponential backoff');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('consecutive_empty_syncs');
        });
    }
};
