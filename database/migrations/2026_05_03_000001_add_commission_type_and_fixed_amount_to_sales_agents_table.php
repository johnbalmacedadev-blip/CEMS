<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_agents', function (Blueprint $table) {
            $table->string('commission_type', 32)->nullable()->after('commission_rate');
            $table->decimal('commission_fixed_amount', 12, 2)->nullable()->after('commission_type');
        });
    }

    public function down(): void
    {
        Schema::table('sales_agents', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_fixed_amount']);
        });
    }
};
