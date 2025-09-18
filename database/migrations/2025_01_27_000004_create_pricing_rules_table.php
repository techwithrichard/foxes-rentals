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
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->enum('rule_type', [
                'commission', 'late_fee', 'deposit', 'renewal_fee', 
                'maintenance_fee', 'processing_fee', 'utility_fee', 
                'parking_fee', 'pet_fee', 'cleaning_fee'
            ])->index();
            $table->json('conditions')->nullable(); // Rule conditions
            $table->enum('calculation_method', [
                'percentage', 'fixed_amount', 'sliding_scale', 
                'per_square_foot', 'per_unit', 'per_room'
            ])->index();
            $table->decimal('value', 10, 2);
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['rule_type', 'is_active']);
            $table->index(['calculation_method', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
