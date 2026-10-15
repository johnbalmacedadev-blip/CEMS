<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('executive_agent_leads', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('executive_agent_id');
            $table->date('application_date')->nullable();
            $table->string('application_source', 100)->nullable();
            $table->string('customer_name', 255);
            $table->string('customer_phone', 100)->nullable();
            $table->string('unit_inquired', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('about_what', 255)->nullable();

            $table->string('followup_1_exec', 100)->nullable();
            $table->date('followup_1_date')->nullable();
            $table->string('followup_1_outcome', 255)->nullable();
            $table->text('followup_1_notes')->nullable();

            $table->string('followup_2_exec', 100)->nullable();
            $table->date('followup_2_date')->nullable();
            $table->string('followup_2_outcome', 255)->nullable();
            $table->text('followup_2_notes')->nullable();

            $table->string('followup_3_exec', 100)->nullable();
            $table->date('followup_3_date')->nullable();
            $table->string('followup_3_outcome', 255)->nullable();
            $table->text('followup_3_notes')->nullable();

            $table->string('followup_4_exec', 100)->nullable();
            $table->date('followup_4_date')->nullable();
            $table->string('followup_4_outcome', 255)->nullable();
            $table->text('followup_4_notes')->nullable();

            $table->string('source_sheet', 100)->nullable();
            $table->unsignedInteger('source_row')->nullable();
            $table->timestamps();

            $table->index(['executive_agent_id', 'application_date']);
            $table->index(['executive_agent_id', 'customer_name']);
            // No FK: executive_agents.id may lack a PRIMARY KEY on some hosted DBs.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('executive_agent_leads');
    }
};
