<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN status ENUM('Available', 'Under Maintenance', 'Reserved', 'Released', 'Forfeited', 'Archived') NOT NULL DEFAULT 'Available'");

        DB::statement("ALTER TABLE vehicle_status_details MODIFY COLUMN sale_status ENUM('Available', 'Under Maintenance', 'Reserved', 'Released', 'Forfeited', 'Archived') NOT NULL DEFAULT 'Available'");

        if (! \Illuminate\Support\Facades\Schema::hasColumn('vehicles', 'archived_at')) {
            DB::statement('ALTER TABLE vehicles ADD COLUMN archived_at TIMESTAMP NULL DEFAULT NULL AFTER status');
        }
        if (! \Illuminate\Support\Facades\Schema::hasColumn('vehicles', 'status_before_archive')) {
            DB::statement("ALTER TABLE vehicles ADD COLUMN status_before_archive VARCHAR(50) NULL DEFAULT NULL AFTER archived_at");
        }
    }

    public function down(): void
    {
        DB::table('vehicles')->where('status', 'Archived')->update(['status' => 'Available', 'archived_at' => null, 'status_before_archive' => null]);

        if (\Illuminate\Support\Facades\Schema::hasColumn('vehicles', 'status_before_archive')) {
            DB::statement('ALTER TABLE vehicles DROP COLUMN status_before_archive');
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('vehicles', 'archived_at')) {
            DB::statement('ALTER TABLE vehicles DROP COLUMN archived_at');
        }

        DB::statement("ALTER TABLE vehicles MODIFY COLUMN status ENUM('Available', 'Under Maintenance', 'Reserved', 'Released', 'Forfeited') NOT NULL DEFAULT 'Available'");
        DB::statement("ALTER TABLE vehicle_status_details MODIFY COLUMN sale_status ENUM('Available', 'Under Maintenance', 'Reserved', 'Released', 'Forfeited') NOT NULL DEFAULT 'Available'");
    }
};
