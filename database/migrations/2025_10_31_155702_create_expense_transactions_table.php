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
        Schema::create('expense_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->decimal('starting_cash', 12, 2)->default(0);
            $table->decimal('added_cash', 12, 2)->default(0);
            $table->decimal('total_cash', 12, 2)->default(0);
            $table->decimal('total_expense', 12, 2)->default(0);
            $table->decimal('cash_remaining', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_transaction_id')->constrained('expense_transactions')->onDelete('cascade');
            $table->string('description');
            $table->string('care_of')->nullable();
            $table->decimal('cost', 12, 2);
            $table->enum('payment_tag', ['Office', 'Vehicle'])->default('Office');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_items');
        Schema::dropIfExists('expense_transactions');
    }
};
