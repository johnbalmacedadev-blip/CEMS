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
        Schema::create('soa_floated_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->date('budget_date');
            $table->date('reference_date');
            $table->decimal('yesterday_closing_balance', 15, 2);
            $table->decimal('declared_starting_balance', 15, 2);
            $table->decimal('difference_amount', 15, 2);
            $table->timestamps();

            $table->unique(['payment_method_id', 'budget_date']);
            $table->index(['payment_method_id', 'reference_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soa_floated_funds');
    }
};
