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
        Schema::create('property_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('rental_property_id')->constrained('rental_properties')->onDelete('cascade');
            $table->foreignUuid('pricing_rule_id')->constrained('pricing_rules')->onDelete('cascade');
            $table->decimal('applied_value', 10, 2);
            $table->timestamp('applied_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['pricing_rule_id', 'applied_at']);
            $table->index(['rental_property_id', 'pricing_rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_pricing_rules');
    }
};
