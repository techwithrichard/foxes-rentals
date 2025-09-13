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
        Schema::create('rental_properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignUuid('property_type_id')->constrained('property_types')->onDelete('cascade');
            $table->foreignUuid('landlord_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('rent_amount', 16, 2);
            $table->decimal('deposit_amount', 16, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->string('electricity_id', 50)->nullable();
            $table->string('water_id', 50)->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->boolean('is_vacant')->default(true);
            $table->boolean('is_multi_unit')->default(false);
            $table->integer('total_units')->default(1);
            $table->integer('available_units')->default(1);
            $table->boolean('furnished')->default(false);
            $table->boolean('pet_friendly')->default(false);
            $table->boolean('smoking_allowed')->default(false);
            $table->integer('parking_spaces')->default(0);
            $table->boolean('balcony')->default(false);
            $table->boolean('garden')->default(false);
            $table->boolean('swimming_pool')->default(false);
            $table->boolean('gym')->default(false);
            $table->boolean('security')->default(false);
            $table->boolean('elevator')->default(false);
            $table->boolean('air_conditioning')->default(false);
            $table->boolean('heating')->default(false);
            $table->boolean('internet')->default(false);
            $table->boolean('cable_tv')->default(false);
            $table->boolean('laundry')->default(false);
            $table->boolean('dishwasher')->default(false);
            $table->boolean('microwave')->default(false);
            $table->boolean('refrigerator')->default(false);
            $table->boolean('stove')->default(false);
            $table->boolean('oven')->default(false);
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->string('floor_plan')->nullable();
            $table->string('virtual_tour')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('year_built')->nullable();
            $table->integer('last_renovated')->nullable();
            $table->decimal('property_size', 10, 2)->nullable();
            $table->decimal('lot_size', 10, 2)->nullable();
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('living_rooms')->default(0);
            $table->integer('kitchens')->default(0);
            $table->integer('dining_rooms')->default(0);
            $table->integer('storage_rooms')->default(0);
            $table->integer('garage_spaces')->default(0);
            $table->integer('outdoor_spaces')->default(0);
            $table->json('utilities_included')->nullable();
            $table->json('maintenance_responsibility')->nullable();
            $table->json('lease_terms')->nullable();
            $table->integer('minimum_lease_period')->default(1);
            $table->integer('maximum_lease_period')->nullable();
            $table->integer('notice_period')->default(30);
            $table->decimal('late_fee_percentage', 5, 2)->default(0);
            $table->decimal('late_fee_fixed', 10, 2)->default(0);
            $table->decimal('returned_check_fee', 10, 2)->default(0);
            $table->decimal('early_termination_fee', 10, 2)->default(0);
            $table->json('renewal_terms')->nullable();
            $table->json('special_conditions')->nullable();
            $table->text('marketing_description')->nullable();
            $table->text('keywords')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'is_vacant']);
            $table->index(['property_type_id']);
            $table->index(['landlord_id']);
            $table->index(['is_featured', 'is_published']);
            $table->index(['rent_amount']);
            $table->index(['bedrooms', 'bathrooms']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_properties');
    }
};
