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
        // Add posted_price column (copy from sales_price if it exists)
        if (Schema::hasColumn('vehicles', 'sales_price')) {
            // If sales_price exists, copy data and add posted_price
            DB::statement('ALTER TABLE vehicles ADD COLUMN posted_price DECIMAL(10,2) NULL AFTER purchase_price');
            DB::statement('UPDATE vehicles SET posted_price = sales_price WHERE sales_price IS NOT NULL');
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('sales_price');
            });
        } else if (!Schema::hasColumn('vehicles', 'posted_price')) {
            // If neither exists, just add posted_price
            Schema::table('vehicles', function (Blueprint $table) {
                $table->decimal('posted_price', 10, 2)->nullable()->after('purchase_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('vehicles', 'posted_price')) {
            // Copy data back to sales_price
            DB::statement('ALTER TABLE vehicles ADD COLUMN sales_price DECIMAL(10,2) NULL AFTER purchase_price');
            DB::statement('UPDATE vehicles SET sales_price = posted_price WHERE posted_price IS NOT NULL');
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('posted_price');
            });
        }
    }
};
