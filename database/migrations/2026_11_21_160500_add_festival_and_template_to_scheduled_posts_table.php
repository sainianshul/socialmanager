<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->foreignId('festival_id')->nullable()->after('account_id')->constrained('festivals')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->after('festival_id')->constrained('festival_templates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->dropForeign(['festival_id']);
            $table->dropForeign(['template_id']);
            $table->dropColumn(['festival_id', 'template_id']);
        });
    }
};
