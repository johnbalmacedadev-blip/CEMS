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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('date_added_to_schedule')->nullable();
            $table->string('added_by', 100)->nullable();
            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->string('customer_phone_number', 50)->nullable();
            $table->string('showroom', 100)->nullable();
            $table->date('date_of_visit')->nullable();
            $table->string('preferred_unit', 500)->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('sales_exec_who_assisted', 100)->nullable();
            $table->string('outcome', 255)->nullable();
            $table->text('notes_of_visit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
