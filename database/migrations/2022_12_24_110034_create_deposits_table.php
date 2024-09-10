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
        Schema::create('deposits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('amount', 16, 2);
            $table->foreignUuid('lease_id')->constrained()->cascadeOnDelete();
            $table->string('status')
                ->index()
                ->default('pending');
            $table->foreignUuid('tenant_id')
                ->nullable()
                ->constrained('users', 'id')
                ->cascadeOnDelete();
            $table->decimal('refund_amount', 8, 2)->nullable();
            $table->date('refund_date')->nullable();
            $table->boolean('refund_paid')->default(false);
            $table->string('refund_receipt')->nullable();
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
        Schema::dropIfExists('deposits');
    }
};
