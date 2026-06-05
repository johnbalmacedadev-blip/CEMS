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
        Schema::create('vehicle_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number'); // Foreign key to vehicles table
            $table->text('paint_items')->nullable();
            $table->decimal('paint_costs', 10, 2)->default(0);
            $table->text('mechanical_electrical_items')->nullable();
            $table->decimal('mechanical_electrical_costs', 10, 2)->default(0);
            $table->text('cluster_items')->nullable();
            $table->decimal('cluster_costs', 10, 2)->default(0);
            $table->text('aircon_items')->nullable();
            $table->decimal('aircon_cost', 10, 2)->default(0);
            $table->text('interior_items')->nullable();
            $table->decimal('interior_costs', 10, 2)->default(0);
            $table->text('papers_items')->nullable();
            $table->decimal('papers_costs', 10, 2)->default(0);
            $table->text('tyres_battery_items')->nullable();
            $table->decimal('tyres_battery_cost', 10, 2)->default(0);
            $table->text('misc_items')->nullable();
            $table->decimal('misc_costs', 10, 2)->default(0);
            $table->text('total_repair_items')->nullable();
            $table->decimal('total_repair_cost', 10, 2)->default(0);
            $table->text('post_reservation_repairs')->nullable();
            $table->decimal('post_reservation_repairs_cost', 10, 2)->default(0);
            $table->decimal('total_capital_repair_capital_posted', 10, 2)->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('plate_number')->references('plate_number')->on('vehicles')->onDelete('cascade');
            $table->index('plate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_expenses');
    }
};