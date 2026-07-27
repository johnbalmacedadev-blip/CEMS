<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mechanic_jobs', function (Blueprint $table) {
            $table->id();
            $table->date('job_date');
            $table->string('job_type')->default('Internal'); // Internal | External
            $table->string('mechanic')->nullable();
            $table->string('category')->nullable(); // Mechanical / Electrical (external)
            $table->string('year_model')->nullable();
            $table->string('plate_number')->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->string('endorse')->nullable();
            $table->text('description')->nullable();
            $table->text('labor')->nullable();
            $table->text('parts')->nullable();
            $table->decimal('parts_cost', 12, 2)->nullable();
            $table->string('status')->nullable();
            $table->string('unit_label')->nullable(); // raw external UNIT text
            $table->timestamps();

            $table->index('job_date');
            $table->index('job_type');
            $table->index('status');
            $table->index('plate_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mechanic_jobs');
    }
};
