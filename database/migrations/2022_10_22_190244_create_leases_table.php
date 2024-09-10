<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('lease_id')->nullable()->unique();
            $table->date('start_date')->nullable();
            $table->foreignUuid('property_id')
                ->nullable()
                ->constrained('properties')
                ->cascadeOnDelete();
            $table->foreignUuid('house_id')
                ->nullable()
                ->constrained('houses', 'id')
                ->cascadeOnDelete();
            $table->foreignUuid('tenant_id')
                ->nullable()
                ->constrained('users', 'id')
                ->cascadeOnDelete();
            $table->decimal('rent', 12, 2);
            $table->datetime('termination_date_notice')->nullable();
            $table->datetime('end_date')->nullable();
            $table->unsignedSmallInteger('rent_cycle')->default(1);
            $table->integer('invoice_generation_day')->default(28);
            $table->date('next_billing_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leases');
    }
};
