<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the category enum to include mixed-use
        DB::statement("ALTER TABLE property_types MODIFY COLUMN category ENUM('residential', 'commercial', 'industrial', 'land', 'mixed-use') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the category enum to original values
        DB::statement("ALTER TABLE property_types MODIFY COLUMN category ENUM('residential', 'commercial', 'industrial', 'land') NOT NULL");
    }
};
