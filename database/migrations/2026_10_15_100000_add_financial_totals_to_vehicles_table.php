<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'total_costs')) {
                $table->decimal('total_costs', 15, 2)->nullable()->after('purchase_price');
            }
            if (! Schema::hasColumn('vehicles', 'total_revenue')) {
                $table->decimal('total_revenue', 15, 2)->nullable()->after('total_costs');
            }
            if (! Schema::hasColumn('vehicles', 'total_profit')) {
                $table->decimal('total_profit', 15, 2)->nullable()->after('total_revenue');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            foreach (['total_costs', 'total_revenue', 'total_profit'] as $col) {
                if (Schema::hasColumn('vehicles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
