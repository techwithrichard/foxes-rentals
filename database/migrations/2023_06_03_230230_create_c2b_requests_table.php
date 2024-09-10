<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('c2b_requests', function (Blueprint $table) {
            $table->id();
            $table->string('TransactionType');
            $table->string('TransID');
            $table->string('TransTime');
            $table->string('TransAmount');
            $table->string('BusinessShortCode');
            $table->string('BillRefNumber')->nullable();
            $table->string('InvoiceNumber')->nullable();
            $table->string('OrgAccountBalance')->nullable();
            $table->string('ThirdPartyTransID')->nullable();
            $table->string('MSISDN');
            $table->string('FirstName')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c2b_requests');
    }
};
