<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('event_type', 50)->index(); // account_sync, post_publish, auto_dm, comment_process, webhook, queue_job
            $table->string('action', 191);             // Brief human-readable description
            $table->string('status', 20)->default('success')->index(); // success, pending, failed
            $table->json('payload')->nullable();       // Request payload or parameters
            $table->longText('response')->nullable();  // Response data or error trace
            $table->unsignedInteger('duration_ms')->nullable(); // Execution time in milliseconds
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            // Compound fast query index
            $table->index(['event_type', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_activity_logs');
    }
};
