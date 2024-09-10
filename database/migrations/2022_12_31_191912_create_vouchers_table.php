<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('voucher_id');
            $table->string('type');
            $table->date('voucher_date');
            $table->text('notes')->nullable();
            $table->foreignUuid('property_id')
                ->nullable()
                ->constrained('properties')
                ->nullOnDelete();
            $table->foreignUuid('house_id')
                ->nullable()
                ->constrained('houses')
                ->nullOnDelete();

            $table->foreignUuid('landlord_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();
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
        Schema::dropIfExists('vouchers');
    }
};
