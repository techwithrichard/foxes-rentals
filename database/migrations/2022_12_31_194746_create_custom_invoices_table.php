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
        Schema::create('custom_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('invoice_id');
            //invoice date, due date,notes,landlord_id,property_id,house_id
            $table->date('invoice_date');
            $table->date('due_date');
            $table->text('notes')->nullable();
            $table->foreignUuid('landlord_id')
                ->nullable()
                ->constrained('users', 'id')
                ->cascadeOnDelete();

            $table->foreignUuid('property_id')
                ->nullable()
                ->constrained('properties', 'id')
                ->onUpdate('cascade');

            $table->foreignUuid('house_id')
                ->nullable()
                ->constrained('houses', 'id')
                ->onUpdate('cascade');

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
        Schema::dropIfExists('custom_invoices');
    }
};
