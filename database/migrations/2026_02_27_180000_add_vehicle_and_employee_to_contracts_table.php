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
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable()->after('contract_type')->constrained('vehicles')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->after('vehicle_id')->constrained('employees')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropForeign(['employee_id']);
        });
    }
};
