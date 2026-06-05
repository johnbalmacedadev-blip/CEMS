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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->string('make');
            $table->string('model');
            $table->string('variant')->nullable();
            $table->enum('transmission', ['Manual', 'Automatic']);
            $table->enum('fuel_type', ['Diesel', 'Gasoline']);
            $table->bigInteger('kilometers');
            $table->string('plate_number')->unique();
            $table->string('colour');
            $table->boolean('with_tools')->default(false);
            $table->boolean('with_matting')->default(false);
            $table->boolean('with_spare_tire')->default(false);
            $table->decimal('purchase_price', 12, 2);
            $table->string('purchased_from');
            $table->date('purchase_date');
            $table->boolean('spare_key')->default(false);
            $table->text('notes')->nullable();
            $table->enum('status', ['Available', 'Sold', 'Maintenance'])->default('Available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};