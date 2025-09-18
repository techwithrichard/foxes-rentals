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
        Schema::create('pricing_rule_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('pricing_rule_id')->constrained('pricing_rules')->onDelete('cascade');
            $table->uuid('applicable_id'); // Polymorphic relation
            $table->string('applicable_type'); // e.g., 'App\Models\RentalProperty'
            $table->decimal('base_amount', 10, 2);
            $table->decimal('calculated_amount', 10, 2);
            $table->json('context')->nullable(); // Additional context data
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['pricing_rule_id', 'created_at']);
            $table->index(['applicable_id', 'applicable_type']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rule_applications');
    }
};
