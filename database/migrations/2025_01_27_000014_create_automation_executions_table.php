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
        Schema::create('automation_executions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('automation_rule_id')->constrained('automation_rules')->onDelete('cascade');
            $table->string('status')->index(); // 'pending', 'running', 'completed', 'failed', 'cancelled', 'timeout'
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('execution_time_ms')->nullable(); // Execution time in milliseconds
            $table->json('trigger_data')->nullable(); // Data that triggered the execution
            $table->json('action_data')->nullable(); // Data used in the action
            $table->text('error_message')->nullable();
            $table->json('execution_log')->nullable(); // Detailed execution log
            $table->unsignedInteger('affected_records_count')->nullable(); // Number of records affected
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['automation_rule_id', 'status']);
            $table->index(['status', 'started_at']);
            $table->index(['automation_rule_id', 'started_at']);
            $table->index(['started_at']);
            $table->index(['completed_at']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_executions');
    }
};
