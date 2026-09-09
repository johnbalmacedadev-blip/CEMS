<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $row = DB::select('SHOW CREATE TABLE miscellaneous_transactions')[0] ?? null;
        $sql = strtoupper($row->{'Create Table'} ?? '');
        if ($sql === '') {
            return;
        }
        if (! str_contains($sql, 'PRIMARY KEY')) {
            DB::statement('ALTER TABLE miscellaneous_transactions ADD PRIMARY KEY (id)');
        }
        if (! str_contains($sql, 'AUTO_INCREMENT')) {
            DB::statement('ALTER TABLE miscellaneous_transactions MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        // Keep autoincrement; removing it would break inserts.
    }
};
