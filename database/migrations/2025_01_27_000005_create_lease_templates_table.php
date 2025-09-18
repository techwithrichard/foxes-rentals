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
        Schema::create('lease_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->enum('template_type', [
                'residential', 'commercial', 'short_term', 'long_term',
                'monthly', 'weekly', 'daily', 'vacation', 'student', 'senior'
            ])->index();
            $table->longText('content'); // Full template content
            $table->json('terms')->nullable(); // Template terms and conditions
            $table->json('variables')->nullable(); // Available variables
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['template_type', 'is_active']);
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_templates');
    }
};
