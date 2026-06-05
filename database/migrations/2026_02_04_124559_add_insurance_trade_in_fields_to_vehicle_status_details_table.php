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
        Schema::table('vehicle_status_details', function (Blueprint $table) {
            $table->boolean('has_insurance')->nullable()->after('transfer_cost');
            $table->decimal('insurance_value', 12, 2)->nullable()->after('has_insurance');
            $table->boolean('has_trade_in')->nullable()->after('insurance_value');
            $table->decimal('trade_in_value', 12, 2)->nullable()->after('has_trade_in');
            $table->integer('days_from_acquisition_to_reservation')->nullable()->after('days_from_reservation_to_release');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_status_details', function (Blueprint $table) {
            $table->dropColumn([
                'has_insurance',
                'insurance_value',
                'has_trade_in',
                'trade_in_value',
                'days_from_acquisition_to_reservation'
            ]);
        });
    }
};
