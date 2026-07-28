<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ReconcileExcelUnitsCommand extends Command
{
    protected $signature = 'units:reconcile-excel
                            {--excel= : Path to (PRIVATE) AVAILABLE-RESERVED-RELEASED UNITS.xlsx}
                            {--skip-export : Only verify snapshot exists; do not re-export}';

    protected $description = 'Export Excel unit tabs to snapshot JSON used by Unit Report mismatch notes';

    public function handle(): int
    {
        $snapshot = storage_path('app/excel_units_snapshot.json');
        $script = base_path('scripts/export_excel_units_snapshot.py');

        if ($this->option('skip-export')) {
            if (! is_file($snapshot)) {
                $this->error('Snapshot missing: '.$snapshot);

                return self::FAILURE;
            }
            $this->info('Snapshot present: '.$snapshot);

            return self::SUCCESS;
        }

        if (! is_file($script)) {
            $this->error('Export script missing: '.$script);

            return self::FAILURE;
        }

        $excel = $this->option('excel');
        $env = null;
        if (is_string($excel) && $excel !== '') {
            $env = array_merge($_ENV, ['EXCEL_UNITS_SRC' => $excel]);
            $this->line('Using Excel: '.$excel);
        }

        $this->info('Exporting Excel tabs to snapshot…');
        $process = new Process(['python', $script], base_path(), $env, null, 300);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->error('Excel export failed.');

            return self::FAILURE;
        }

        if (! is_file($snapshot)) {
            $this->error('Snapshot was not created.');

            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($snapshot), true);
        $this->info('Snapshot ready: '.$snapshot);
        foreach ($data['designations'] ?? [] as $d) {
            $this->line(sprintf(
                ' - %s: rows=%s unique=%s dups=%s',
                $d['tab'] ?? $d['key'],
                $d['excel_row_count'] ?? 0,
                $d['excel_unique_plates'] ?? 0,
                $d['duplicate_plate_count'] ?? 0
            ));
        }

        return self::SUCCESS;
    }
}
