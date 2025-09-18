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
        // Settings Categories Table
        Schema::create('settings_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Settings Groups Table
        Schema::create('settings_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('settings_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Enhanced Settings Items Table
        Schema::create('settings_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('group_id')->constrained('settings_groups')->onDelete('cascade');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['text', 'number', 'boolean', 'select', 'multiselect', 'file', 'json', 'email', 'url', 'password'])->default('text');
            $table->json('validation_rules')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_required')->default(false);
            $table->text('default_value')->nullable();
            $table->json('options')->nullable(); // For select/multiselect options
            $table->string('placeholder')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Settings History Table
        Schema::create('settings_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('setting_id')->constrained('settings_items')->onDelete('cascade');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignUuid('changed_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('changed_at');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // User Preferences Table
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('preference_key');
            $table->text('preference_value')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'preference_key']);
        });

        // Environment Settings Table
        Schema::create('environment_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('environment'); // local, staging, production
            $table->string('setting_key');
            $table->text('setting_value')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();
            
            $table->unique(['environment', 'setting_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('environment_settings');
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('settings_history');
        Schema::dropIfExists('settings_items');
        Schema::dropIfExists('settings_groups');
        Schema::dropIfExists('settings_categories');
    }
};
