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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();

            // Meta Post Identifiers
            $table->string('meta_post_id')->index()->comment('Facebook or Instagram Post ID');

            // Content & Media
            $table->text('message')->nullable()->comment('Post text / caption');
            $table->string('media_type')->nullable()->comment('photo, video, album, link, reel, status');
            $table->text('media_url')->nullable()->comment('Primary media asset or thumbnail URL');
            $table->text('permalink_url')->nullable()->comment('Public web URL to view post on Meta');

            // Engagement Metrics
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('comments_count')->default(0);
            $table->unsignedBigInteger('shares_count')->default(0);

            // Timestamps & Sync State
            $table->timestamp('published_at')->nullable()->comment('When published on Meta platform');
            $table->timestamp('last_comment_sync_at')->nullable()->comment('Last time comments were fetched');

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate records for the same post on the same account
            $table->unique(['account_id', 'meta_post_id'], 'account_meta_post_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

