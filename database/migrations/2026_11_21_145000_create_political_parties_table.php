<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('political_parties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_code', 50)->unique();
            $table->string('symbol_logo')->nullable();
            $table->string('top_leaders_strip')->nullable();
            $table->string('primary_color', 20)->default('#FF9933');
            $table->string('secondary_color', 20)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('political_parties');
    }
};
