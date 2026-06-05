<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_orcr', function (Blueprint $table) {
            $table->string('remark', 255)->nullable()->after('transaction_type');
            $table->string('others_note', 100)->nullable()->after('others');
        });

        foreach (DB::table('transfer_orcr')->where('remarks', 'like', 'OTHERS:%')->get() as $row) {
            $note = trim(preg_replace('/^OTHERS:\s*/i', '', $row->remarks ?? ''));
            DB::table('transfer_orcr')->where('id', $row->id)->update([
                'others_note' => $note ?: null,
                'remarks' => null,
            ]);
        }

        DB::table('transfer_orcr')
            ->whereIn('remarks', ['VALID ID', 'VALID ID '])
            ->update(['remark' => 'VALID ID', 'remarks' => null]);
    }

    public function down(): void
    {
        Schema::table('transfer_orcr', function (Blueprint $table) {
            $table->dropColumn(['remark', 'others_note']);
        });
    }
};
