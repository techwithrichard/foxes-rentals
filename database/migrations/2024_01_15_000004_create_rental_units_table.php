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
        Schema::create('rental_units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rental_property_id')->constrained('rental_properties')->onDelete('cascade');
            $table->string('unit_number', 50);
            $table->string('unit_name')->nullable();
            $table->integer('floor_number')->nullable();
            $table->decimal('rent_amount', 16, 2);
            $table->decimal('deposit_amount', 16, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->boolean('is_vacant')->default(true);
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->decimal('square_footage', 10, 2)->nullable();
            $table->boolean('balcony')->default(false);
            $table->boolean('parking_space')->default(false);
            $table->boolean('storage_unit')->default(false);
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->text('notes')->nullable();
            $table->text('maintenance_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['rental_property_id', 'unit_number']);
            $table->index(['status', 'is_vacant']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_units');
    }
};
