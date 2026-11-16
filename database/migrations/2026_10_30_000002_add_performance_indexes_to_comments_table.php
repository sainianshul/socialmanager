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
        Schema::table('comments', function (Blueprint $table) {
            $table->index('commented_at', 'comments_commented_at_idx');
            $table->index(['account_id', 'commented_at'], 'comments_acc_commented_idx');
            $table->index(['account_id', 'status', 'commented_at'], 'comments_acc_status_commented_idx');
            $table->index('sentiment', 'comments_sentiment_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_commented_at_idx');
            $table->dropIndex('comments_acc_commented_idx');
            $table->dropIndex('comments_acc_status_commented_idx');
            $table->dropIndex('comments_sentiment_idx');
        });
    }
};

