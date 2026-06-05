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
        Schema::table('gas_expenses', function (Blueprint $table) {
            // Drop existing foreign key if it exists
            $table->dropForeign(['plate_number']);
        });
        
        Schema::table('gas_expenses', function (Blueprint $table) {
            // Add foreign key constraint without cascade for now
            $table->foreign('plate_number')->references('plate_number')->on('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gas_expenses', function (Blueprint $table) {
            $table->dropForeign(['plate_number']);
        });
    }
};
