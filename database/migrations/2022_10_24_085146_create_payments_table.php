<?php

use App\Enums\PaymentStatusEnum;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('amount', 16, 2);
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('payment_receipt')->nullable();
            $table->foreignUuid('invoice_id')
                ->nullable()
                ->constrained('invoices', 'id')
                ->cascadeOnDelete();
            $table->foreignUuid('tenant_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignUuid('landlord_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('property_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignUuid('house_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('commission', 16, 2);
            $table->foreignUuid('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status')
                ->default(PaymentStatusEnum::PENDING->value)
                ->index();


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
        Schema::dropIfExists('payments');
    }
};
