<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('client_type', 30)->nullable()->default(null)->change();
            $table->boolean('can_manage_users')->default(false)->after('ai_quota_resets_at');
            $table->boolean('can_manage_content')->default(true)->after('can_manage_users');
            $table->boolean('can_manage_tickets')->default(false)->after('can_manage_content');
            $table->boolean('can_access_system')->default(false)->after('can_manage_tickets');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_manage_users',
                'can_manage_content',
                'can_manage_tickets',
                'can_access_system',
            ]);
            $table->string('client_type', 30)->nullable(false)->default('politician')->change();
        });
    }
};
