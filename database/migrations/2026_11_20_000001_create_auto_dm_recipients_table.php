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
        Schema::create('auto_dm_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('commenter_id', 120)->index()->comment('Meta commenter ID or identifier');
            $table->timestamp('last_dm_sent_at')->index()->comment('Timestamp of last dispatched Auto-DM');
            $table->unsignedInteger('dm_count')->default(1)->comment('Lifetime Auto-DMs sent to this user');
            $table->timestamps();

            // Strict unique constraint: One tracking record per commenter per account
            $table->unique(['account_id', 'commenter_id'], 'account_commenter_dm_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_dm_recipients');
    }
};
