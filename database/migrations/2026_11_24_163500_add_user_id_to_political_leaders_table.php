<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('political_leaders', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('party_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->index(['party_id', 'user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('political_leaders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['party_id', 'user_id', 'is_active']);
            $table->dropColumn('user_id');
        });
    }
};
