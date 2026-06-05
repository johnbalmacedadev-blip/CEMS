<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_agent_commissions', function (Blueprint $table) {
            $table->foreignId('sales_agent_id')
                ->nullable()
                ->after('id')
                ->constrained('sales_agents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales_agent_commissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sales_agent_id');
        });
    }
};
