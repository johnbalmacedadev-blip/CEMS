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
        Schema::table('vehicles', function (Blueprint $table) {
            // Add foreign key columns
            $table->foreignId('make_id')->nullable()->after('year')->constrained()->onDelete('cascade');
            $table->foreignId('model_id')->nullable()->after('make_id')->constrained()->onDelete('cascade');
            
            // Keep the original make and model columns for now (we'll remove them later)
            // $table->dropColumn(['make', 'model']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['make_id']);
            $table->dropForeign(['model_id']);
            $table->dropColumn(['make_id', 'model_id']);
        });
    }
};