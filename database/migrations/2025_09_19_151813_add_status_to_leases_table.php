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
        Schema::table('leases', function (Blueprint $table) {
            $table->enum('status', ['active', 'expired', 'terminated', 'pending'])->default('pending')->after('rent');
        });

        // Update existing leases with appropriate status based on dates
        DB::statement("
            UPDATE leases 
            SET status = CASE 
                WHEN end_date IS NOT NULL AND end_date < CURDATE() THEN 'expired'
                WHEN termination_date_notice IS NOT NULL THEN 'terminated'
                WHEN start_date IS NOT NULL AND start_date <= CURDATE() THEN 'active'
                ELSE 'pending'
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
