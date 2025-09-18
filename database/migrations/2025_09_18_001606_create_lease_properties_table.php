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
        Schema::create('lease_properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignUuid('property_type_id')->nullable()->constrained('property_types')->onDelete('set null');
            $table->foreignUuid('landlord_id')->constrained('users')->onDelete('cascade');
            $table->decimal('lease_amount', 10, 2);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'pending', 'sold'])->default('active');
            $table->boolean('is_available')->default(true);
            $table->integer('lease_duration_months')->default(12);
            $table->integer('minimum_lease_period')->nullable();
            $table->integer('maximum_lease_period')->nullable();
            $table->text('renewal_terms')->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('year_built')->nullable();
            $table->decimal('property_size', 10, 2)->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->json('lease_terms')->nullable();
            $table->json('special_conditions')->nullable();
            $table->text('marketing_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_properties');
    }
};
