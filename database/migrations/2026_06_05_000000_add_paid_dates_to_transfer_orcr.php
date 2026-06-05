<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_orcr', function (Blueprint $table) {
            $table->date('transfer_sop_paid_date')->nullable()->after('transfer_sop_paid');
            $table->date('transfer_or_paid_date')->nullable()->after('transfer_or_paid');
            $table->date('pnp_clearance_paid_date')->nullable()->after('pnp_clearance_paid');
            $table->date('rd_sop_paid_date')->nullable()->after('rd_sop_paid');
            $table->date('rd_or_paid_date')->nullable()->after('rd_or_paid');
        });
    }

    public function down(): void
    {
        Schema::table('transfer_orcr', function (Blueprint $table) {
            $table->dropColumn([
                'transfer_sop_paid_date',
                'transfer_or_paid_date',
                'pnp_clearance_paid_date',
                'rd_sop_paid_date',
                'rd_or_paid_date',
            ]);
        });
    }
};
