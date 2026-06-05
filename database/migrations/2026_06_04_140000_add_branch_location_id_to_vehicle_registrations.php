<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_registrations', function (Blueprint $table) {
            $table->foreignId('branch_location_id')
                ->nullable()
                ->after('id')
                ->constrained('branch_locations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_registrations', function (Blueprint $table) {
            $table->dropForeign(['branch_location_id']);
            $table->dropColumn('branch_location_id');
        });
    }
};
