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
        Schema::table('expense_items', function (Blueprint $table) {
            $table->date('expense_date')->nullable()->after('expense_transaction_id');
            $table->foreignId('payment_method_id')->nullable()->after('expense_date')->constrained('payment_methods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_items', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['expense_date', 'payment_method_id']);
        });
    }
};
