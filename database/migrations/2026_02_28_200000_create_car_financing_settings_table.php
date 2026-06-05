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
        Schema::create('car_financing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('year_model_range', 50)->unique()->comment('e.g. 2026-2022, 2021-2020');
            $table->decimal('chattel_fee', 12, 2)->default(0);
            $table->decimal('insurance_initial', 12, 2)->default(0);
            $table->decimal('no_pdc_charge', 12, 2)->default(0);
            $table->decimal('term_pct_12', 8, 4)->default(0.153)->comment('15.30% as 0.153');
            $table->decimal('term_pct_24', 8, 4)->default(0.306);
            $table->decimal('term_pct_36', 8, 4)->default(0.459);
            $table->decimal('term_pct_48', 8, 4)->default(0.612);
            $table->decimal('term_pct_60', 8, 4)->default(0.72);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_financing_settings');
    }
};
