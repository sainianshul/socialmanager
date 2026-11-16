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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();

            // Meta Comment Identifiers
            $table->string('meta_comment_id')->index()->comment('Facebook or Instagram Comment ID');
            $table->foreignId('parent_comment_id')->nullable()->constrained('comments')->nullOnDelete()->comment('Parent comment if this is a nested reply');

            // Commenter Details
            $table->string('commenter_name')->comment('Display name of commenter');
            $table->string('commenter_meta_id')->nullable()->index()->comment('Meta User ID of commenter');

            // Content
            $table->text('message')->comment('Original comment content');

            // AI Sentiment & Political Intelligence
            $table->string('sentiment')->nullable()->comment('positive, negative, neutral');
            $table->decimal('sentiment_score', 4, 2)->nullable()->comment('Confidence score 0.00 to 1.00');
            $table->string('category')->nullable()->comment('appreciation, complaint, question, suggestion, hate_speech, spam, general');
            $table->boolean('is_abusive')->default(false)->comment('Flagged by AI as abusive / toxic');
            $table->decimal('abuse_confidence', 4, 2)->nullable()->comment('Abuse confidence 0.00 to 1.00');
            $table->boolean('is_favor')->default(false)->comment('Supports politician / policy');
            $table->boolean('is_against')->default(false)->comment('Criticizes politician / policy');
            $table->boolean('is_complaint')->default(false)->comment('Constituent grievance / complaint');
            $table->boolean('should_hide')->default(false)->comment('AI recommended hiding this comment');
            $table->boolean('reply_needed')->default(true)->comment('Whether a reply is recommended');

            // AI Reply & Moderation Workflow
            // Status: 0: New, 1: Pending Review, 2: Replied, 3: Hidden, 4: Flagged, 5: Ignored
            $table->unsignedTinyInteger('status')->default(0)->index()->comment('0: New, 1: Pending Review, 2: Replied, 3: Hidden, 4: Flagged, 5: Ignored');
            $table->text('ai_reply')->nullable()->comment('Draft or published AI reply text');
            $table->string('meta_reply_id')->nullable()->comment('Meta Comment ID of our reply');

            // Timestamps
            $table->timestamp('replied_at')->nullable()->comment('When reply was published on Meta');
            $table->timestamp('hidden_at')->nullable()->comment('When comment was hidden on Meta');
            $table->timestamp('commented_at')->nullable()->comment('When comment was posted on Meta');

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate insertion of the same Meta comment for a post
            $table->unique(['post_id', 'meta_comment_id'], 'post_meta_comment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

