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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('ticket_id');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open');
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignUuid('assigned_to')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUuid('property_id')
                ->nullable()
                ->constrained('properties')
                ->cascadeOnDelete();

            $table->foreignUuid('house_id')
                ->nullable()
                ->constrained('houses', 'id')
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
        Schema::dropIfExists('support_tickets');
    }
};
