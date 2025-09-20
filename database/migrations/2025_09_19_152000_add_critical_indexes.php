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
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('phone');
            $table->index('is_active');
            $table->index(['is_active', 'created_at']);
        });

        // Properties table indexes
        Schema::table('properties', function (Blueprint $table) {
            $table->index(['status', 'landlord_id']);
            $table->index(['is_vacant', 'status']);
            $table->index('rent');
            $table->index('created_at');
        });

        // Leases table indexes
        Schema::table('leases', function (Blueprint $table) {
            $table->index(['status', 'tenant_id']);
            $table->index(['status', 'property_id']);
            $table->index(['start_date', 'end_date']);
            $table->index('next_billing_date');
        });

        // Payments table indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
            $table->index(['payment_method', 'status']);
            $table->index('reference_number');
            $table->index(['tenant_id', 'status']);
        });

        // Invoices table indexes
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['status', 'tenant_id']);
            $table->index(['status', 'due_date']);
            $table->index(['landlord_id', 'status']);
            $table->index('due_date');
        });

        // Houses table indexes
        Schema::table('houses', function (Blueprint $table) {
            $table->index(['property_id', 'status']);
            $table->index(['is_vacant', 'status']);
        });

        // Support tickets indexes
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->index(['status', 'user_id']);
            $table->index(['priority', 'status']);
            $table->index('created_at');
        });

        // Rental properties indexes
        Schema::table('rental_properties', function (Blueprint $table) {
            $table->index(['status', 'is_vacant']);
            $table->index(['landlord_id', 'status']);
            $table->index(['rent_amount', 'status']);
        });

        // Sale properties indexes
        Schema::table('sale_properties', function (Blueprint $table) {
            $table->index(['status', 'is_available']);
            $table->index(['landlord_id', 'status']);
            $table->index(['sale_price', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_active', 'created_at']);
        });

        // Properties table indexes
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['status', 'landlord_id']);
            $table->dropIndex(['is_vacant', 'status']);
            $table->dropIndex(['rent']);
            $table->dropIndex(['created_at']);
        });

        // Leases table indexes
        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex(['status', 'tenant_id']);
            $table->dropIndex(['status', 'property_id']);
            $table->dropIndex(['start_date', 'end_date']);
            $table->dropIndex(['next_billing_date']);
        });

        // Payments table indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['payment_method', 'status']);
            $table->dropIndex(['reference_number']);
            $table->dropIndex(['tenant_id', 'status']);
        });

        // Invoices table indexes
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status', 'tenant_id']);
            $table->dropIndex(['status', 'due_date']);
            $table->dropIndex(['landlord_id', 'status']);
            $table->dropIndex(['due_date']);
        });

        // Houses table indexes
        Schema::table('houses', function (Blueprint $table) {
            $table->dropIndex(['property_id', 'status']);
            $table->dropIndex(['is_vacant', 'status']);
        });

        // Support tickets indexes
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropIndex(['status', 'user_id']);
            $table->dropIndex(['priority', 'status']);
            $table->dropIndex(['created_at']);
        });

        // Rental properties indexes
        Schema::table('rental_properties', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_vacant']);
            $table->dropIndex(['landlord_id', 'status']);
            $table->dropIndex(['rent_amount', 'status']);
        });

        // Sale properties indexes
        Schema::table('sale_properties', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_available']);
            $table->dropIndex(['landlord_id', 'status']);
            $table->dropIndex(['sale_price', 'status']);
        });
    }
};
