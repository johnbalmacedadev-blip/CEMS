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
        Schema::create('gas_expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('driver');
            $table->string('model');
            $table->string('plate_number');
            $table->decimal('gas_amount', 10, 2);
            $table->string('expense_sent_by'); // MERLIN/ALYSSA
            $table->boolean('has_photo_video_in_groupchat')->default(false);
            $table->boolean('photo_fuel_gauge_before')->default(false);
            $table->boolean('photo_fuel_gauge_after')->default(false);
            $table->boolean('photo_car_license_plate_gas_boy')->default(false);
            $table->boolean('photo_receipt_next_to_gas_pump')->default(false);
            $table->string('checked_by');
            $table->timestamps();
            
            // Add foreign key constraint to vehicles table
            $table->foreign('plate_number')->references('plate_number')->on('vehicles')->onDelete('cascade');
            
            // Add index for better performance
            $table->index('plate_number');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_expenses');
    }
};
