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
        // Clear existing categories and insert new ones
        DB::table('vehicle_expense_categories')->truncate();
        
        // Insert updated categories
        $categories = [
            'Paint',
            'Mechanical / Electrical',
            'Cluster',
            'Aircon',
            'Interior',
            'Paper',
            'Tyers',
            'Battery',
            'Miscellaneous',
            'Repair',
            'Post Reservation Repairs'
        ];
        
        $now = now();
        $insertData = array_map(function($category) use ($now) {
            return [
                'name' => $category,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }, $categories);
        
        DB::table('vehicle_expense_categories')->insert($insertData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore original categories
        DB::table('vehicle_expense_categories')->truncate();
        
        DB::table('vehicle_expense_categories')->insert([
            ['name' => 'Gas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paint', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maintenance', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Remote Battery', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mags', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};
