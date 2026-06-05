<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_orcr_other_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_orcr_id')->constrained('transfer_orcr')->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->boolean('paid')->default(false);
            $table->date('paid_date')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_orcr_other_transactions');
    }
};
