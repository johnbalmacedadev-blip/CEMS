<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_registrations', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->decimal('renewal_reg_or', 12, 2)->nullable();
            $table->decimal('renewal_sop', 12, 2)->nullable();
            $table->decimal('smoke_na', 12, 2)->nullable();
            $table->decimal('duplicate_plate', 12, 2)->nullable();
            $table->decimal('migrate', 12, 2)->nullable();
            $table->decimal('duplicate_cr', 12, 2)->nullable();
            $table->decimal('pnp_clearance', 12, 2)->nullable();
            $table->decimal('confirmation', 12, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('coc_no', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_registrations');
    }
};
