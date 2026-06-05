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
        Schema::create('client_follow_up_list', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->date('follow_up_date')->nullable();
            $table->string('status')->default('Pending'); // Pending, Contacted, In Progress, Closed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_follow_up_list');
    }
};
