<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_documents', function (Blueprint $table) {
            $table->foreignId('agent_bolo_agent_id')->nullable()->after('type')->constrained('agent_bolo_agents')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('company_documents', function (Blueprint $table) {
            $table->dropForeign(['agent_bolo_agent_id']);
        });
    }
};
