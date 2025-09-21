<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update property types category enum to support expert-designed categories
     */
    public function up(): void
    {
        // First, add new enum values one by one to avoid conflicts
        DB::statement("ALTER TABLE property_types MODIFY COLUMN category ENUM('residential', 'commercial', 'industrial', 'land', 'mixed-use', 'office', 'retail', 'hospitality', 'healthcare') NOT NULL");
        
        // Update existing 'commercial' records to more specific categories
        DB::table('property_types')
            ->where('category', 'commercial')
            ->where('name', 'LIKE', '%Office%')
            ->update(['category' => 'office']);
            
        DB::table('property_types')
            ->where('category', 'commercial')
            ->where('name', 'LIKE', '%Retail%')
            ->update(['category' => 'retail']);
            
        DB::table('property_types')
            ->where('category', 'commercial')
            ->where('name', 'LIKE', '%Restaurant%')
            ->update(['category' => 'retail']);
            
        DB::table('property_types')
            ->where('category', 'commercial')
            ->where('name', 'LIKE', '%Hotel%')
            ->update(['category' => 'hospitality']);
            
        DB::table('property_types')
            ->where('category', 'commercial')
            ->where('name', 'LIKE', '%Medical%')
            ->update(['category' => 'healthcare']);
            
        // Update remaining commercial to office as default
        DB::table('property_types')
            ->where('category', 'commercial')
            ->update(['category' => 'office']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the previous category enum
        DB::statement("ALTER TABLE property_types MODIFY COLUMN category ENUM('residential', 'commercial', 'industrial', 'land', 'mixed-use') NOT NULL");
    }
};
