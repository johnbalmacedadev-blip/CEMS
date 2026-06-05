<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Insert default categories
        DB::table('vehicle_expense_categories')->insert([
            ['name' => 'Gas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paint', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maintenance', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Remote Battery', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mags', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_expense_categories');
    }
};

