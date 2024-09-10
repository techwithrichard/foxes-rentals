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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamp('password_changed_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('identity_no')->nullable();
            $table->string('identity_document')->nullable();
            $table->string('occupation_status')->nullable();
            $table->string('occupation_place')->nullable();
            $table->string('kin_name')->nullable();
            $table->string('kin_identity')->nullable();
            $table->string('kin_phone')->nullable();
            $table->string('kin_relationship')->nullable();
            $table->string('emergency_name')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_email')->nullable();
            $table->string('emergency_relationship')->nullable();
            $table->string('address')->nullable();
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('welcome_valid_until')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
