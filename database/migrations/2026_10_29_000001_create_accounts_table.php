<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Account Type (Platform): short integer for constants
            // 1: Facebook Page, 2: Instagram Business, 3: Twitter/X, 4: YouTube, 5: LinkedIn
            $table->unsignedTinyInteger('account_type')->default(1)->comment('1: Facebook, 2: Instagram, etc.');

            // Meta Platform IDs
            $table->string('meta_account_id')->index()->comment('Facebook Page ID or Instagram Account ID');
            $table->string('instagram_business_account_id')->nullable()->index()->comment('Linked Instagram Business ID for FB Page');

            // Profile / Page Details
            $table->string('name')->comment('Page or Account Display Name');
            $table->string('username')->nullable()->comment('@handle or page username');
            $table->string('category')->nullable()->default('Public Figure')->comment('e.g. Politician, Public Figure');
            $table->text('profile_pic_url')->nullable();
            $table->unsignedBigInteger('followers_count')->default(0);
            $table->unsignedBigInteger('following_count')->default(0);

            // Auth Tokens & Permissions
            $table->text('access_token')->comment('Long-lived page or user access token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();

            // Status & Sync
            $table->boolean('is_active')->default(true);
            $table->boolean('webhook_active')->default(false);
            $table->timestamp('last_synced_at')->nullable();

            // Political AI Bot / War Room Customization
            $table->string('ai_tone')->default('Humble & Respectful')->comment('e.g. Humble & Respectful, Assertive, Patriotic, Inspiring');
            $table->string('ai_language')->default('Hindi + Hinglish')->comment('Language preference for AI replies');
            $table->boolean('auto_reply_mode')->default(true)->comment('true: Full Auto, false: Approval/Review required');
            $table->boolean('auto_hide_trolls')->default(true)->comment('Auto-hide abusive/negative comments');
            $table->boolean('comment_to_dm')->default(false)->comment('Auto-send private DM to engaged constituents');

            // Flexible Metadata & Settings (blocked keywords, custom prompt notes, etc.)
            $table->json('settings')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate account connection for the same user & platform
            $table->unique(['user_id', 'account_type', 'meta_account_id'], 'user_platform_account_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

