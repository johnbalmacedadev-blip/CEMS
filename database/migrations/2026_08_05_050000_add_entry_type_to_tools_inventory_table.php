<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tools_inventory', function (Blueprint $table) {
            $table->string('entry_type', 20)->default('purchase')->after('date_acquired');
            $table->index(['entry_type', 'date_acquired']);
        });

        // Existing amount>0 rows are purchases; amount=0 inventory snapshots from prior import
        DB::table('tools_inventory')->where('amount', '>', 0)->update(['entry_type' => 'purchase']);
        DB::table('tools_inventory')->where('amount', '<=', 0)->update(['entry_type' => 'inventory']);
    }

    public function down(): void
    {
        Schema::table('tools_inventory', function (Blueprint $table) {
            $table->dropIndex(['entry_type', 'date_acquired']);
            $table->dropColumn('entry_type');
        });
    }
};
