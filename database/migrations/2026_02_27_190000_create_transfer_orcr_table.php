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
        Schema::create('transfer_orcr', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('transaction_type', 50)->default('ORCR');
            $table->date('release_date')->nullable();
            $table->string('lto_file_no', 100)->nullable();
            $table->decimal('transfer_sop', 12, 2)->default(0);
            $table->decimal('transfer_or', 12, 2)->default(0);
            $table->decimal('others', 12, 2)->nullable();
            $table->decimal('notary', 12, 2)->nullable();
            $table->decimal('pnp_clearance', 12, 2)->default(0);
            $table->decimal('confirmation', 12, 2)->nullable();
            $table->string('rd', 100)->nullable();
            $table->decimal('rd_sop', 12, 2)->nullable();
            $table->decimal('rd_or', 12, 2)->nullable();
            $table->decimal('renewal_reg_or', 12, 2)->nullable();
            $table->decimal('renewal_sop', 12, 2)->nullable();
            $table->string('smoke_na', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 50)->default('Pending'); // Pending, In Progress, DONE
            $table->boolean('transfer_sop_paid')->default(false);
            $table->boolean('transfer_or_paid')->default(false);
            $table->boolean('pnp_clearance_paid')->default(false);
            $table->boolean('rd_sop_paid')->default(false);
            $table->boolean('rd_or_paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_orcr');
    }
};
