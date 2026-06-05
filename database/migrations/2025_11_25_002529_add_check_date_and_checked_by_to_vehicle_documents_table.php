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
        Schema::table('vehicle_documents', function (Blueprint $table) {
            $table->date('check_date')->nullable()->after('notes');
            $table->string('checked_by')->nullable()->after('check_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_documents', function (Blueprint $table) {
            $table->dropColumn(['check_date', 'checked_by']);
        });
    }
};
