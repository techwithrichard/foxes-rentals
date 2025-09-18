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
        Schema::create('property_amenities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('category')->index();
            $table->string('icon')->nullable();
            $table->boolean('is_chargeable')->default(false);
            $table->decimal('default_cost', 10, 2)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['category', 'sort_order']);
            $table->index(['is_active', 'is_chargeable']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_amenities');
    }
};
