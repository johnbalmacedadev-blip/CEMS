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
        Schema::table('soa_manual_entries', function (Blueprint $table) {
            $table->boolean('is_expense_budget')->default(false)->after('credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soa_manual_entries', function (Blueprint $table) {
            $table->dropColumn('is_expense_budget');
        });
    }
};
