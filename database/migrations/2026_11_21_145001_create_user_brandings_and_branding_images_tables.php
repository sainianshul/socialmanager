<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_brandings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('client_type', 30)->default('politician')->index();
            $table->foreignId('party_id')->nullable()->constrained('political_parties')->nullOnDelete();
            $table->string('display_name');
            $table->string('designation')->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->string('social_handle', 100)->nullable();
            $table->string('website')->nullable();
            $table->string('frame_style', 50)->default('classic');
            $table->boolean('include_senior_leaders')->default(true);
            $table->boolean('is_default')->default(true)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('branding_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_branding_id')->nullable()->constrained('user_brandings')->cascadeOnDelete();
            $table->string('title', 150)->nullable();
            $table->string('original_image_path');
            $table->string('cutout_image_path')->nullable();
            $table->boolean('is_default')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branding_images');
        Schema::dropIfExists('user_brandings');
    }
};
