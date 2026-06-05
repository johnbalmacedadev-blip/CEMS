<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_agent_commissions', function (Blueprint $table) {
            $table->string('showroom', 100)->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('sales_agent_commissions', function (Blueprint $table) {
            $table->dropColumn('showroom');
        });
    }
};
