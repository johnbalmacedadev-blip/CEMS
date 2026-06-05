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
        Schema::create('daily_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->date('budget_date');
            $table->decimal('starting_balance', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of payment_method_id and budget_date
            $table->unique(['payment_method_id', 'budget_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_budgets');
    }
};
