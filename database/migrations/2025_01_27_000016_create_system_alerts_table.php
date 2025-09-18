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
        Schema::create('system_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('alert_type')->index(); // e.g., 'threshold_exceeded', 'service_down'
            $table->string('severity')->index(); // 'info', 'warning', 'error', 'critical'
            $table->string('title');
            $table->text('message');
            $table->string('source')->nullable(); // e.g., 'system_monitor', 'performance_monitor'
            $table->string('metric_name')->nullable(); // The metric that triggered the alert
            $table->decimal('threshold_value', 10, 4)->nullable(); // The threshold that was exceeded
            $table->decimal('actual_value', 10, 4)->nullable(); // The actual value that triggered the alert
            $table->string('status')->default('active')->index(); // 'active', 'acknowledged', 'resolved', 'suppressed'
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignUuid('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignUuid('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable(); // Additional metadata
            $table->json('tags')->nullable(); // Additional tags for filtering
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['alert_type', 'status']);
            $table->index(['severity', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['source', 'status']);
            $table->index(['created_at']);
            $table->index(['acknowledged_at']);
            $table->index(['resolved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_alerts');
    }
};
