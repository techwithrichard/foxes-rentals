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
        Schema::create('landlord_remittances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('landlord_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('paid_on');
            $table->date('period_from');
            $table->date('period_to');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('remarks')->nullable();
            $table->string('payment_receipt')->nullable();
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
        Schema::dropIfExists('landlord_remittances');
    }
};
