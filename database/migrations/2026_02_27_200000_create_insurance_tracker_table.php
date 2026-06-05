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
        Schema::create('insurance_tracker', function (Blueprint $table) {
            $table->id();
            $table->string('showroom', 100)->nullable();
            $table->string('sales', 100)->nullable();
            $table->string('year', 20)->nullable();
            $table->string('make', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('number', 50)->nullable(); // plate or chassis number
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->string('transaction', 50)->nullable(); // e.g. CASH
            $table->string('source', 255)->nullable();
            $table->date('reservation_date')->nullable();
            $table->date('release_date')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_tracker');
    }
};
