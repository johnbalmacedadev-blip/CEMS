<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tables that commonly lose PRIMARY KEY / AUTO_INCREMENT after dump/import.
     */
    private array $tables = [
        'users',
        'user_page_permissions',
        'activity_logs',
        'migrations',
        'miscellaneous_transactions',
        'personal_access_tokens',
        'password_reset_tokens',
        'failed_jobs',
        'jobs',
        'sessions',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            $this->ensureAutoIncrementId($table);
        }
    }

    public function down(): void
    {
        // Keep autoincrement; removing it would break inserts.
    }

    private function ensureAutoIncrementId(string $table): void
    {
        try {
            $row = DB::select('SHOW CREATE TABLE `'.$table.'`')[0] ?? null;
        } catch (\Throwable) {
            return;
        }

        $sql = strtoupper($row->{'Create Table'} ?? '');
        if ($sql === '') {
            return;
        }

        // Only touch tables that have an `id` column.
        $create = $row->{'Create Table'} ?? '';
        if (! preg_match('/^\s*`id`\s+/im', $create)) {
            return;
        }

        if (! str_contains($sql, 'PRIMARY KEY')) {
            DB::statement("ALTER TABLE `{$table}` ADD PRIMARY KEY (`id`)");
        }

        if (! str_contains($sql, 'AUTO_INCREMENT')) {
            DB::statement("ALTER TABLE `{$table}` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        }
    }
};
