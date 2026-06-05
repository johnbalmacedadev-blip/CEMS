<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_agent_commissions', function (Blueprint $table) {
            $table->decimal('agents_folder_amount', 12, 2)->nullable()->after('amount');
            $table->decimal('sales_executive_commission', 12, 2)->nullable()->after('agents_folder_amount');
            $table->boolean('proof_of_appointment')->default(false)->after('sales_executive_commission');
            $table->boolean('sign_client_with_agent')->default(false)->after('proof_of_appointment');
            $table->date('date_of_payment')->nullable()->after('date_sent');
        });
    }

    public function down(): void
    {
        Schema::table('sales_agent_commissions', function (Blueprint $table) {
            $table->dropColumn([
                'agents_folder_amount',
                'sales_executive_commission',
                'proof_of_appointment',
                'sign_client_with_agent',
                'date_of_payment',
            ]);
        });
    }
};
