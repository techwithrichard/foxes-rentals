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
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('rule_type')->index(); // e.g., 'invoicing', 'reminders', 'lease_management'
            $table->string('trigger_type')->index(); // e.g., 'event_based', 'schedule_based', 'condition_based'
            $table->json('trigger_conditions')->nullable(); // JSON for trigger configuration
            $table->string('action_type')->index(); // e.g., 'send_email', 'create_invoice', 'update_status'
            $table->json('action_parameters')->nullable(); // JSON for action configuration
            $table->json('target_conditions')->nullable(); // JSON for target filtering
            $table->boolean('is_active')->default(true)->index();
            $table->integer('priority')->default(5)->index(); // 1-10 priority scale
            $table->unsignedBigInteger('execution_count')->default(0);
            $table->timestamp('last_executed_at')->nullable();
            $table->timestamp('next_execution_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['rule_type', 'is_active']);
            $table->index(['trigger_type', 'is_active']);
            $table->index(['action_type', 'is_active']);
            $table->index(['is_active', 'next_execution_at']);
            $table->index(['priority', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_rules');
    }
};
