<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('soa_manual_entries', function (Blueprint $table) {
            $table->string('expense_budget_tier', 32)->nullable()->after('is_expense_budget');
        });

        DB::table('soa_manual_entries')
            ->where('is_expense_budget', true)
            ->whereNull('expense_budget_tier')
            ->update(['expense_budget_tier' => 'flagship']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soa_manual_entries', function (Blueprint $table) {
            $table->dropColumn('expense_budget_tier');
        });
    }
};
