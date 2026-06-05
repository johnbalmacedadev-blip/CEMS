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
        Schema::table('car_financing_settings', function (Blueprint $table) {
            $table->decimal('chattel_fee_percent', 5, 2)->nullable()->after('chattel_fee')->comment('% of Amount Financed (AF)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_financing_settings', function (Blueprint $table) {
            $table->dropColumn('chattel_fee_percent');
        });
    }
};
