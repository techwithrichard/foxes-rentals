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
            // Add new detailed status fields
            $table->string('detailed_status')->nullable()->after('status');
            $table->string('result_code')->nullable()->after('detailed_status');
            $table->text('result_description')->nullable()->after('result_code');
            $table->json('callback_metadata')->nullable()->after('result_description');
            $table->timestamp('status_updated_at')->nullable()->after('callback_metadata');
            $table->string('failure_reason')->nullable()->after('status_updated_at');
            
            // Add index for better performance
            $table->index(['detailed_status', 'created_at']);
            $table->index('result_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stk_requests', function (Blueprint $table) {
            $table->dropIndex(['detailed_status', 'created_at']);
            $table->dropIndex(['result_code']);
            $table->dropColumn([
                'detailed_status',
                'result_code', 
                'result_description',
                'callback_metadata',
                'status_updated_at',
                'failure_reason'
            ]);
        });
    }
};