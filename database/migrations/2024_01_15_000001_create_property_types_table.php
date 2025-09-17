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
        // Check if table exists, if not create it, if yes modify it
        if (!Schema::hasTable('property_types')) {
            Schema::create('property_types', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('category', ['residential', 'commercial', 'industrial', 'land']);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->string('icon')->nullable();
                $table->string('color', 7)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            // Table exists, add missing columns
            Schema::table('property_types', function (Blueprint $table) {
                if (!Schema::hasColumn('property_types', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('property_types', 'category')) {
                    $table->enum('category', ['residential', 'commercial', 'industrial', 'land']);
                }
                if (!Schema::hasColumn('property_types', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('property_types', 'sort_order')) {
                    $table->integer('sort_order')->default(0);
                }
                if (!Schema::hasColumn('property_types', 'icon')) {
                    $table->string('icon')->nullable();
                }
                if (!Schema::hasColumn('property_types', 'color')) {
                    $table->string('color', 7)->nullable();
                }
                if (!Schema::hasColumn('property_types', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_types');
    }
};
