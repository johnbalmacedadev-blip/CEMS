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
        Schema::table('vehicle_status_details', function (Blueprint $table) {
            $table->string('financing_company', 100)->nullable()->after('cash_financing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_status_details', function (Blueprint $table) {
            $table->dropColumn('financing_company');
        });
    }
};
