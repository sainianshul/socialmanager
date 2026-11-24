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
        Schema::create('poster_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                // e.g. "सामाजिक जागरूकता", "दैनिक सुविचार"
            $table->string('slug')->unique();                      // "social-awareness", "suvichar"
            $table->string('icon')->nullable();                    // "ti ti-sun", "ti ti-heart-handshake"
            $table->boolean('show_as_collection')->default(false); // क्या टॉप कलेक्शन में अलग से कार्ड दिखाना है
            $table->integer('sort_order')->default(0);             // Display order
            $table->boolean('is_active')->default(true);           // Active / Inactive
            $table->timestamps();
        });

        Schema::create('posters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('poster_categories')->cascadeOnDelete();
            $table->foreignId('party_id')->nullable()->constrained('political_parties')->nullOnDelete(); 
            // party_id: NULL = Sabhi users ke liye (Universal), or specific to BJP/SP/Congress

            $table->string('name');                                // e.g. "बेटी बचाओ बेटी पढ़ाओ"
            $table->string('slug')->nullable();
            $table->string('image_path');                          // Uploaded photo ka path
            $table->string('thumbnail_path')->nullable();          // Fast preview thumbnail
            $table->text('default_caption')->nullable();           // 1-Click auto-fill caption & hashtags
            
            // Auto & Trending controls
            $table->boolean('show_on_top')->default(false);        // Top par pin karne ke liye
            $table->date('auto_show_date')->nullable();            // Special date par auto-top lane ke liye
            $table->unsignedInteger('usage_count')->default(0);    // Most popular / Trending tracking

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Fast search indexes
            $table->index(['category_id', 'is_active']);
            $table->index(['party_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posters');
        Schema::dropIfExists('poster_categories');
    }
};
