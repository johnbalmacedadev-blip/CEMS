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
            $table->string('requested_by')->nullable()->after('care_of');
            $table->string('approved_by')->nullable()->after('requested_by');
            $table->string('store_shop')->nullable()->after('approved_by');
            $table->boolean('receipt_checked')->default(false)->after('store_shop');
            $table->string('receipt_checker')->nullable()->after('receipt_checked');
            $table->date('receipt_check_date')->nullable()->after('receipt_checker');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_items', function (Blueprint $table) {
            $table->dropColumn([
                'requested_by',
                'approved_by',
                'store_shop',
                'receipt_checked',
                'receipt_checker',
                'receipt_check_date'
            ]);
        });
    }
};
