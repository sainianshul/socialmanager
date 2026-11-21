<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('festivals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('festival_date')->index();
            $table->string('category', 50)->default('general')->index();
            $table->text('ready_caption')->nullable();
            $table->text('hashtags')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('festival_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained('festivals')->cascadeOnDelete();
            $table->foreignId('party_id')->nullable()->constrained('political_parties')->nullOnDelete();
            $table->string('title');
            $table->string('template_image_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('photo_position', 30)->default('bottom-right');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festival_templates');
        Schema::dropIfExists('festivals');
    }
};
