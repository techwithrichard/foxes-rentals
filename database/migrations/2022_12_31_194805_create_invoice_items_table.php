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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('quantity');
            $table->text('description');
            $table->decimal('cost', 8, 2);
            $table->foreignUuid('custom_invoice_id')
                ->nullable()
                ->constrained('custom_invoices', 'id')
                ->cascadeOnDelete();
            $table->string('invoice_scanned_copy')->nullable();


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
        Schema::dropIfExists('invoice_items');
    }
};
