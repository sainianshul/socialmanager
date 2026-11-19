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
        Schema::create('auto_dm_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name', 120);
            $table->json('keywords');
            $table->string('match_type', 20)->default('contains'); // 'contains' or 'exact'
            $table->text('dm_message');
            $table->text('public_reply')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('trigger_count')->default(0);
            $table->timestamp('last_triggered_at')->nullable();

            $table->timestamps();

            // Indexes for fast lookup on webhook comment events
            $table->index(['account_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
            $table->index('post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_dm_rules');
    }
};
