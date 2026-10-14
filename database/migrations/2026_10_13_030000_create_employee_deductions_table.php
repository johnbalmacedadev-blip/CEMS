<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->unsignedSmallInteger('year')->index();
            $table->string('period_key', 40);
            $table->string('period_label', 100);
            $table->string('category', 40)->default('contribution'); // contribution|sss|philhealth|pagibig
            $table->decimal('ee_share', 12, 2)->nullable();
            $table->decimal('er_share', 12, 2)->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('source_sheet', 120)->nullable();
            $table->timestamps();

            $table->index(['year', 'last_name', 'first_name']);
            $table->index(['employee_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_deductions');
    }
};
