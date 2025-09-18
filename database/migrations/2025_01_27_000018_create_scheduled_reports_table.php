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
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('report_type')->index(); // e.g., 'property_report', 'financial_report'
            $table->foreignUuid('template_id')->nullable()->constrained('report_templates')->onDelete('set null');
            $table->json('filters')->nullable(); // Report filters
            $table->string('schedule_frequency')->index(); // 'daily', 'weekly', 'monthly', 'quarterly', 'yearly'
            $table->time('schedule_time'); // Time to run the report
            $table->json('recipients')->nullable(); // Email recipients
            $table->string('export_format')->default('pdf')->index(); // 'pdf', 'excel', 'csv', 'json'
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable()->index();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['is_active', 'next_run_at']);
            $table->index(['schedule_frequency', 'is_active']);
            $table->index(['export_format', 'is_active']);
            $table->index(['created_by', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_reports');
    }
};
