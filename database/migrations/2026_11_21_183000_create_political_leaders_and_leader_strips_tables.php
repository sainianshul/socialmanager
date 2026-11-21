<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Political Leaders Master Bank
        Schema::create('political_leaders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('political_parties')->cascadeOnDelete();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('image')->nullable();
            $table->string('state', 100)->default('National')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 2. Leader Strips (Senior Leadership Header Banners)
        Schema::create('leader_strips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('political_parties')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('is_system')->default(false)->index(); // True for system-provided presets
            $table->timestamps();
        });

        // 3. Strip Items Pivot with Hierarchy / Left-to-Right Sequence
        Schema::create('leader_strip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leader_strip_id')->constrained('leader_strips')->cascadeOnDelete();
            $table->foreignId('political_leader_id')->constrained('political_leaders')->cascadeOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['leader_strip_id', 'political_leader_id'], 'strip_leader_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leader_strip_items');
        Schema::dropIfExists('leader_strips');
        Schema::dropIfExists('political_leaders');
    }
};
