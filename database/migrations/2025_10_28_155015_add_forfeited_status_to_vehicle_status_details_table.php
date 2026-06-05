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
        // Update the ENUM to include 'Forfeited' status
        DB::statement("ALTER TABLE vehicle_status_details MODIFY COLUMN sale_status ENUM('Available', 'Under Maintenance', 'Reserved', 'Released', 'Forfeited') NOT NULL DEFAULT 'Available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the previous enum values (remove 'Forfeited')
        DB::statement("ALTER TABLE vehicle_status_details MODIFY COLUMN sale_status ENUM('Available', 'Under Maintenance', 'Reserved', 'Released') NOT NULL DEFAULT 'Available'");
    }
};