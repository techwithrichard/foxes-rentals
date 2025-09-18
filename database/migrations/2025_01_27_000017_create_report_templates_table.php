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
        Schema::create('report_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->index(); // e.g., 'financial', 'property', 'tenant'
            $table->string('report_type')->index(); // e.g., 'property_report', 'financial_report'
            $table->json('sections')->nullable(); // Template sections configuration
            $table->json('filters')->nullable(); // Available filters
            $table->json('layout')->nullable(); // Layout configuration
            $table->boolean('is_public')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['category', 'is_active']);
            $table->index(['report_type', 'is_active']);
            $table->index(['is_public', 'is_active']);
            $table->index(['created_by', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};
