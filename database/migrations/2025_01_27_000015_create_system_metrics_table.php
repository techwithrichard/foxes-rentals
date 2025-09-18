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
        Schema::create('system_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('metric_type')->index(); // e.g., 'cpu_usage', 'memory_usage', 'disk_usage'
            $table->string('metric_name'); // e.g., 'CPU Usage', 'Memory Usage'
            $table->decimal('value', 10, 4); // The metric value
            $table->string('unit')->nullable(); // e.g., 'percent', 'bytes', 'milliseconds'
            $table->string('category')->index(); // e.g., 'performance', 'system', 'application'
            $table->json('tags')->nullable(); // Additional tags for filtering
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamp('timestamp')->index(); // When the metric was recorded
            $table->string('server_id')->nullable()->index(); // Server identifier if multiple servers
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['metric_type', 'timestamp']);
            $table->index(['category', 'timestamp']);
            $table->index(['server_id', 'timestamp']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_metrics');
    }
};
