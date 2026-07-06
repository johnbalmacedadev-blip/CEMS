<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trail_form_clients', function (Blueprint $table) {
            $table->id();
            $table->date('inquiry_date')->nullable();
            $table->string('client_name');
            $table->string('contact_number', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('status', 30)->default('Inquiring');
            $table->string('inquiry_source', 100)->nullable();
            $table->string('vehicle_type', 100)->nullable();
            $table->string('vehicle_interest')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trail_form_clients');
    }
};
