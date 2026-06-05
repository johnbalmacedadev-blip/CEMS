<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_agents', function (Blueprint $table) {
            $table->foreignId('executive_agent_id')
                ->nullable()
                ->after('sales_agent_id')
                ->constrained('executive_agents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales_agents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('executive_agent_id');
        });
    }
};
