<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_status_details', function (Blueprint $table) {
            $table->string('sales_agent_name', 255)->nullable()->after('sale_origin');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_status_details', function (Blueprint $table) {
            $table->dropColumn('sales_agent_name');
        });
    }
};
