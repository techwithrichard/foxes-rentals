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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->decimal('amount', 16, 2)->nullable();
            $table->decimal('bills_amount', 16, 2)->nullable();
            $table->decimal('paid_amount', 16, 2)->default(0);
            $table->foreignUuid('tenant_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUuid('property_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('house_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status')->index()->default(\App\Enums\PaymentStatusEnum::PENDING->value);

            $table->json('bills')->nullable();


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
        Schema::dropIfExists('invoices');
    }
};
