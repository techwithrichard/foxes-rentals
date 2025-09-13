<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stk_requests', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
            $table->uuid('invoice_id')->nullable()->after('user_id');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stk_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['invoice_id']);
            $table->dropColumn(['user_id', 'invoice_id']);
        });
    }
};
