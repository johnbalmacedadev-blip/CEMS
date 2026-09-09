<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('miscellaneous_transactions', function (Blueprint $table) {
            $table->string('location', 80)->nullable()->after('transaction_date')->index();
        });

        // Existing Annex workbook imports had no location; treat them as Annex.
        DB::table('miscellaneous_transactions')
            ->whereNull('location')
            ->update(['location' => 'Annex']);
    }

    public function down(): void
    {
        Schema::table('miscellaneous_transactions', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
