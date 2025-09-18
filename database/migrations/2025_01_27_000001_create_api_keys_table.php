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
        Schema::create('api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('service_name')->index();
            $table->enum('key_type', [
                'api_key', 'secret', 'token', 'webhook_url', 
                'client_id', 'client_secret', 'public_key', 'private_key'
            ])->default('api_key');
            $table->enum('environment', ['production', 'staging', 'development'])->default('development');
            $table->text('encrypted_value'); // Encrypted API key value
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_used_at')->nullable();
            $table->foreignUuid('last_used_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('expires_at')->nullable();
            $table->integer('usage_count')->default(0);
            $table->integer('rate_limit')->nullable(); // Requests per minute
            $table->json('allowed_ips')->nullable(); // Allowed IP addresses
            $table->timestamps();

            // Indexes for better performance
            $table->index(['service_name', 'environment']);
            $table->index(['is_active', 'expires_at']);
            $table->index('last_used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
