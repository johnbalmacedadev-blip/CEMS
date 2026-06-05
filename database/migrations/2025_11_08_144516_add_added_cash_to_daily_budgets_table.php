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
        Schema::table('daily_budgets', function (Blueprint $table) {
            $table->decimal('added_cash', 12, 2)->default(0)->after('starting_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_budgets', function (Blueprint $table) {
            $table->dropColumn('added_cash');
        });
    }
};
