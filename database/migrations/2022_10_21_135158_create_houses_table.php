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
        Schema::create('houses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->decimal('rent', 16, 2);
            $table->decimal('deposit', 16, 2)->nullable();
            $table->boolean('is_vacant')->default(1);
            $table->smallInteger('status')->default(0)->index();
            $table->foreignUuid('property_id')
                ->constrained('properties');
            $table->foreignUuid('landlord_id')
                ->nullable()
                ->constrained('users');
            $table->decimal('commission', 16, 2);
            $table->string('electricity_id', 16)->nullable();
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
        Schema::dropIfExists('houses');
    }
};
