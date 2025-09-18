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
        Schema::create('rental_property_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('rental_property_id')->constrained('rental_properties')->onDelete('cascade');
            $table->foreignUuid('amenity_id')->constrained('property_amenities')->onDelete('cascade');
            $table->decimal('cost', 10, 2)->nullable();
            $table->boolean('is_included')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate amenities for same property
            $table->unique(['rental_property_id', 'amenity_id']);
            
            // Indexes for better performance
            $table->index(['amenity_id', 'is_included']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_property_amenities');
    }
};
