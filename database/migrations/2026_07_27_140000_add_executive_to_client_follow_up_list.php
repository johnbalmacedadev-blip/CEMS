<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_follow_up_list', function (Blueprint $table) {
            if (! Schema::hasColumn('client_follow_up_list', 'executive_agent_id')) {
                $table->foreignId('executive_agent_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('executive_agents')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('client_follow_up_list', 'team_lead')) {
                $table->string('team_lead', 100)->nullable()->after('executive_agent_id');
            }
            if (! Schema::hasColumn('client_follow_up_list', 'sales_exec_5')) {
                $table->string('sales_exec_5', 100)->nullable();
                $table->date('date_followed_up_5')->nullable();
                $table->string('outcome_5', 255)->nullable();
                $table->text('notes_5')->nullable();
            }
        });

        DB::statement('ALTER TABLE client_follow_up_list MODIFY application VARCHAR(255) NULL');
        DB::statement('ALTER TABLE client_follow_up_list MODIFY contact_number VARCHAR(100) NULL');
        DB::statement('ALTER TABLE client_follow_up_list MODIFY about_what VARCHAR(500) NULL');
    }

    public function down(): void
    {
        Schema::table('client_follow_up_list', function (Blueprint $table) {
            if (Schema::hasColumn('client_follow_up_list', 'sales_exec_5')) {
                $table->dropColumn(['sales_exec_5', 'date_followed_up_5', 'outcome_5', 'notes_5']);
            }
            if (Schema::hasColumn('client_follow_up_list', 'team_lead')) {
                $table->dropColumn('team_lead');
            }
            if (Schema::hasColumn('client_follow_up_list', 'executive_agent_id')) {
                $table->dropConstrainedForeignId('executive_agent_id');
            }
        });
    }
};
