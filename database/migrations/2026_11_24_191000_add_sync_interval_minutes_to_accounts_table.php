<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('accounts', 'sync_interval_minutes')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->unsignedSmallInteger('sync_interval_minutes')->default(0)->after('last_synced_at')->comment('0: Smart Auto-Adaptive (15m to 360m / 6h), >0: manual fixed minutes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('accounts', 'sync_interval_minutes')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn('sync_interval_minutes');
            });
        }
    }
};
