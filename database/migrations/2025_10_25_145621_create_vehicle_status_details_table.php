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
        Schema::create('vehicle_status_details', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number'); // Foreign key to vehicles table
            $table->string('showroom')->nullable();
            $table->date('sale_date')->nullable();
            $table->decimal('sales_price', 12, 2)->nullable();
            $table->decimal('sale_reservation_amount', 12, 2)->nullable();
            $table->string('sales_person_reserved')->nullable();
            $table->string('sales_person_release')->nullable();
            $table->boolean('good_sales_review')->nullable();
            $table->enum('cash_financing', ['Cash', 'Financing'])->nullable();
            $table->string('sale_origin')->nullable();
            $table->decimal('agent_cost', 12, 2)->default(0);
            $table->decimal('finance_revenue_1', 12, 2)->nullable();
            $table->decimal('finance_revenue_2', 12, 2)->nullable();
            $table->enum('sale_status', ['Available', 'Under Maintenance', 'Reserved', 'Released'])->default('Available');
            $table->decimal('transfer_cost', 12, 2)->nullable();
            $table->date('release_date')->nullable();
            $table->integer('days_from_reservation_to_release')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('plate_number')->references('plate_number')->on('vehicles')->onDelete('cascade');
            
            // Index for better performance
            $table->index('plate_number');
            $table->index('sale_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_status_details');
    }
};