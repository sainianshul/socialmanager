<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_schedule_posts')->default(true)->after('canva_token_expires_at');
            $table->boolean('can_use_festival_studio')->default(true)->after('can_schedule_posts');
            $table->boolean('can_use_auto_dm')->default(true)->after('can_use_festival_studio');
            $table->boolean('can_use_ai')->default(true)->after('can_use_auto_dm');
            $table->unsignedBigInteger('ai_token_quota')->nullable()->default(100000)->after('can_use_ai')->comment('Monthly AI token quota. Null = unlimited');
            $table->unsignedBigInteger('ai_tokens_used')->default(0)->after('ai_token_quota')->comment('AI tokens consumed in current billing cycle');
            $table->timestamp('ai_quota_resets_at')->nullable()->after('ai_tokens_used')->comment('Timestamp when monthly token usage automatically resets');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_schedule_posts',
                'can_use_festival_studio',
                'can_use_auto_dm',
                'can_use_ai',
                'ai_token_quota',
                'ai_tokens_used',
                'ai_quota_resets_at',
            ]);
        });
    }
};
