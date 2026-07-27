<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units_masterlist', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('list_number')->nullable()->index();
            $table->string('make_model');
            $table->string('plate_number')->nullable()->index();
            $table->string('variant')->nullable();
            $table->string('transmission')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('year')->nullable();
            $table->unsignedInteger('mileage')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->text('low_down_payment_option')->nullable();
            $table->text('low_monthly_option')->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units_masterlist');
    }
};
