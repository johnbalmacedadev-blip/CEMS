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
        Schema::table('vehicles', function (Blueprint $table) {
            // Option 1 (Low Down Payment)
            $table->decimal('option1_cash_out', 12, 2)->nullable()->after('sold_price');
            $table->decimal('option1_12mos', 12, 2)->nullable()->after('option1_cash_out');
            $table->decimal('option1_24mos', 12, 2)->nullable()->after('option1_12mos');
            $table->decimal('option1_36mos', 12, 2)->nullable()->after('option1_24mos');
            $table->decimal('option1_48mos', 12, 2)->nullable()->after('option1_36mos');
            // Option 2 (Low Monthly Payment)
            $table->decimal('option2_cash_out', 12, 2)->nullable()->after('option1_48mos');
            $table->decimal('option2_12mos', 12, 2)->nullable()->after('option2_cash_out');
            $table->decimal('option2_24mos', 12, 2)->nullable()->after('option2_12mos');
            $table->decimal('option2_36mos', 12, 2)->nullable()->after('option2_24mos');
            $table->decimal('option2_48mos', 12, 2)->nullable()->after('option2_36mos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'option1_cash_out', 'option1_12mos', 'option1_24mos', 'option1_36mos', 'option1_48mos',
                'option2_cash_out', 'option2_12mos', 'option2_24mos', 'option2_36mos', 'option2_48mos',
            ]);
        });
    }
};
