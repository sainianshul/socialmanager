<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('card_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->nullable()->constrained('political_parties')->nullOnDelete();
            $table->string('title');
            $table->string('category', 60)->default('general')->index();
            $table->string('template_image_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('photo_position', 30)->default('bottom-right');
            $table->string('preset_key', 60)->nullable()->default('classic_political_right');
            $table->json('layout_config')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_templates');
    }
};
