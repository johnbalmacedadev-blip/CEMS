<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mechanic_expense_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_type', 20); // parts | external
            $table->string('description');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('repaired_by')->nullable();
            $table->string('unit_label')->nullable();
            $table->date('expense_date');
            $table->timestamps();

            $table->index(['record_type', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mechanic_expense_records');
    }
};
