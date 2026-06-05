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
        Schema::create('sales_agent_commissions', function (Blueprint $table) {
            $table->id();
            $table->string('agent_name');
            $table->string('client_name')->nullable();
            $table->string('unit', 255)->nullable(); // e.g. "2013 FORD EXPLORER"
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->string('plate_number', 50)->nullable();
            $table->string('transaction_type', 50)->default('CASH'); // CASH, FINANCING
            $table->date('release_date')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('coms_paid_via', 100)->nullable(); // BDO, BPI, METROBANK, CASH, GCASH/CASH
            $table->date('date_sent')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_agent_commissions');
    }
};
