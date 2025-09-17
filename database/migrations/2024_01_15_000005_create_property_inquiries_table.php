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
        Schema::create('property_inquiries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('property_id');
            $table->string('property_type');
            $table->string('inquirer_name');
            $table->string('inquirer_email');
            $table->string('inquirer_phone', 20);
            $table->text('message');
            $table->enum('status', ['pending', 'in_progress', 'qualified', 'unqualified', 'closed'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->string('source')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('response')->nullable();
            $table->timestamp('response_date')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_qualified')->default(false);
            $table->string('budget_range')->nullable();
            $table->date('move_in_date')->nullable();
            $table->json('special_requirements')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'priority']);
            $table->index(['property_id', 'property_type']);
            $table->index(['assigned_to']);
            $table->index(['is_qualified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_inquiries');
    }
};
