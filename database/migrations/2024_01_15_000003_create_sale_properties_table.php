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
        Schema::create('sale_properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignUuid('property_type_id')->constrained('property_types')->onDelete('cascade');
            $table->foreignUuid('landlord_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('sale_price', 16, 2);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'sold', 'pending'])->default('active');
            $table->boolean('is_available')->default(true);
            $table->boolean('furnished')->default(false);
            $table->boolean('pet_friendly')->default(false);
            $table->integer('parking_spaces')->default(0);
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('year_built')->nullable();
            $table->decimal('property_size', 10, 2)->nullable();
            $table->decimal('lot_size', 10, 2)->nullable();
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('garage_spaces')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('offers_count')->default(0);
            $table->json('sale_terms')->nullable();
            $table->json('special_conditions')->nullable();
            $table->text('marketing_description')->nullable();
            $table->text('keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'is_available']);
            $table->index(['property_type_id']);
            $table->index(['landlord_id']);
            $table->index(['is_featured', 'is_published']);
            $table->index(['sale_price']);
            $table->index(['bedrooms', 'bathrooms']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_properties');
    }
};
