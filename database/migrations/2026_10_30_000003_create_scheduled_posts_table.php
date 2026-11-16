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
        Schema::create('scheduled_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();

            // Post type: text, photo, video, reel, carousel
            $table->string('post_type', 30)->default('photo')->index();

            // Caption / Content
            $table->text('content')->nullable();

            // Media files array (JSON: ["uploads/posts/1.jpg", ...])
            $table->json('media_urls')->nullable();

            // Target scheduled publication timestamp
            $table->timestamp('scheduled_at')->index();

            // Actual published timestamp
            $table->timestamp('published_at')->nullable()->index();

            // Status: 0 = Draft, 1 = Scheduled, 2 = Publishing, 3 = Published, 4 = Failed, 5 = Cancelled
            $table->unsignedTinyInteger('status')->default(1)->index();

            // Error tracking if Meta API rejects
            $table->text('error_message')->nullable();

            // Meta post ID once published on Meta
            $table->string('meta_post_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Performance index for scheduler queries
            $table->index(['status', 'scheduled_at'], 'idx_scheduled_status_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_posts');
    }
};

