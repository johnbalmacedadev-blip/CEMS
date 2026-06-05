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
        Schema::create('vehicle_incentives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->unique()->constrained('vehicles')->onDelete('cascade');
            $table->string('sa_origin')->nullable();
            $table->string('sa_origin_link')->nullable();
            $table->string('sa_origin_file_path')->nullable();
            $table->string('reserved_by')->nullable();
            $table->boolean('no_look')->default(false);
            $table->string('no_look_link')->nullable();
            $table->string('no_look_file_path')->nullable();
            $table->boolean('insurance')->default(false);
            $table->string('insurance_link')->nullable();
            $table->string('insurance_file_path')->nullable();
            $table->boolean('testimonial')->default(false);
            $table->string('testimonial_link')->nullable();
            $table->string('testimonial_file_path')->nullable();
            $table->boolean('review')->default(false);
            $table->string('review_link')->nullable();
            $table->string('review_file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_incentives');
    }
};
