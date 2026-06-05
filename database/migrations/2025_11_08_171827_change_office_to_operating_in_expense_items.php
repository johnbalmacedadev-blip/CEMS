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
        // First, modify the enum column to include 'Operating' (keeping 'Office' temporarily)
        DB::statement("ALTER TABLE expense_items MODIFY COLUMN payment_tag ENUM('Office', 'Operating', 'Vehicle') DEFAULT 'Office'");
        
        // Update existing 'Office' records to 'Operating'
        DB::table('expense_items')
            ->where('payment_tag', 'Office')
            ->update(['payment_tag' => 'Operating']);
        
        // Now modify the enum column to remove 'Office' and keep only 'Operating' and 'Vehicle'
        DB::statement("ALTER TABLE expense_items MODIFY COLUMN payment_tag ENUM('Operating', 'Vehicle') DEFAULT 'Operating'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'Operating' records back to 'Office'
        DB::table('expense_items')
            ->where('payment_tag', 'Operating')
            ->update(['payment_tag' => 'Office']);
        
        // Revert the enum column back to 'Office'
        DB::statement("ALTER TABLE expense_items MODIFY COLUMN payment_tag ENUM('Office', 'Vehicle') DEFAULT 'Office'");
    }
};
