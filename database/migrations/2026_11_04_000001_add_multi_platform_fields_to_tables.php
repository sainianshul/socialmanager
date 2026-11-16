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
        // 1. Expand accounts table for YouTube & future platforms
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('meta_account_id')->nullable()->change();
            $table->string('youtube_channel_id')->nullable()->index()->after('instagram_business_account_id');
            $table->text('google_refresh_token')->nullable()->after('refresh_token');
            $table->unsignedBigInteger('subscriber_count')->default(0)->after('following_count');
            $table->unsignedBigInteger('video_count')->default(0)->after('subscriber_count');
            $table->json('platform_data')->nullable()->after('settings');
        });

        // 2. Expand posts table for YouTube videos & multi-platform posts
        Schema::table('posts', function (Blueprint $table) {
            $table->string('meta_post_id')->nullable()->change();
            $table->string('platform_post_id')->nullable()->index()->after('meta_post_id');
            $table->string('title', 500)->nullable()->after('message');
            $table->text('description')->nullable()->after('title');
            $table->unsignedInteger('duration')->default(0)->after('description')->comment('Duration in seconds');
            $table->unsignedBigInteger('views_count')->default(0)->after('duration');
            $table->text('thumbnail_url')->nullable()->after('media_url');
            $table->json('tags')->nullable()->after('thumbnail_url');
            $table->string('privacy_status', 20)->default('public')->after('tags');
        });

        // 3. Expand scheduled_posts for multi-platform scheduling
        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->unsignedTinyInteger('platform')->default(1)->index()->after('account_id')->comment('1: FB, 2: IG, 3: YT');
            $table->string('title', 500)->nullable()->after('post_type');
            $table->text('description')->nullable()->after('content');
            $table->json('tags')->nullable()->after('media_urls');
            $table->string('privacy_status', 20)->default('public')->after('tags');
            $table->string('thumbnail_path', 500)->nullable()->after('tags');
        });

        // 4. Expand comments table for platform comment IDs and likes
        Schema::table('comments', function (Blueprint $table) {
            $table->string('meta_comment_id')->nullable()->change();
            $table->string('platform_comment_id')->nullable()->index()->after('meta_comment_id');
            $table->unsignedInteger('likes_count')->default(0)->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['platform_comment_id', 'likes_count']);
        });

        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->dropColumn(['platform', 'title', 'description', 'tags', 'privacy_status', 'thumbnail_path']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'platform_post_id',
                'title',
                'description',
                'duration',
                'views_count',
                'thumbnail_url',
                'tags',
                'privacy_status',
            ]);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn([
                'youtube_channel_id',
                'google_refresh_token',
                'subscriber_count',
                'video_count',
                'platform_data',
            ]);
        });
    }
};

