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
        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('role')->default('tenant');
            $table->foreignUuid('invited_by')->constrained('users')->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled', 'expired'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->foreignUuid('accepted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignUuid('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('expired_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['email', 'status']);
            $table->index(['token']);
            $table->index(['expires_at']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_invitations');
    }
};

