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
        // First, update all existing vehicles to 'Available' status
        DB::table('vehicles')->update(['status' => 'Available']);
        
        // Then modify the column to use the new enum values
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN status ENUM('Available', 'Under Maintenance', 'Reserved', 'Released') NOT NULL DEFAULT 'Available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the old enum values
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN status ENUM('Available', 'Sold', 'Maintenance') NOT NULL DEFAULT 'Available'");
    }
};