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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('amount', 16, 2);
            $table->date('incurred_on');
            $table->string('description')->nullable();
            $table->string('receipt')->nullable();
            $table->foreignUuid('expense_type_id')
                ->nullable()
                ->constrained('expense_types', 'id')
                ->nullOnDelete();

            $table->foreignUuid('landlord_id')
                ->nullable()
                ->constrained('users', 'id')
                ->cascadeOnDelete();
            $table->foreignUuid('property_id')
                ->nullable()
                ->constrained('properties', 'id')
                ->cascadeOnDelete();
            $table->foreignUuid('house_id')
                ->nullable()
                ->constrained('houses', 'id')
                ->cascadeOnDelete();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('expenses');
    }
};
