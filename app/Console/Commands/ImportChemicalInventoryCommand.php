<?php

namespace App\Console\Commands;

use App\Models\ChemicalMovement;
use App\Models\ChemicalStock;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportChemicalInventoryCommand extends Command
{
    protected $signature = 'import:chemical-inventory
                            {--annex= : Path to CHEMICAL INVENTORY - ANNEX.xlsx}
                            {--premium= : Path to CHEMICAL INVENTORY - PREMIUM.xlsx}
                            {--fresh : Truncate chemical tables before import}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import CURRENT STOCK and INCOMING/OUTGOING sheets from Annex and Premium chemical inventory workbooks';

    public function handle(): int
    {
        $files = [
            'Annex' => $this->option('annex'),
            'Premium' => $this->option('premium'),
        ];

        foreach ($files as $location => $path) {
            if (! $path || ! is_file($path)) {
                $this->error("Provide a valid --".strtolower($location)."= path.");

                return self::FAILURE;
            }
        }

        $allStocks = [];
        $allMovements = [];

        foreach ($files as $location => $path) {
            $parsed = $this->parseWorkbook($path, $location);
            $this->info("{$location}: stocks=".count($parsed['stocks']).', movements='.count($parsed['movements']));
            $allStocks = array_merge($allStocks, $parsed['stocks']);
            $allMovements = array_merge($allMovements, $parsed['movements']);
        }

        $this->info('Total stocks: '.count($allStocks));
        $this->info('Total movements: '.count($allMovements));

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            ChemicalStock::query()->truncate();
            ChemicalMovement::query()->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->warn('chemical_stocks + chemical_movements truncated.');
        }

        foreach ($allStocks as $row) {
            ChemicalStock::create($row);
        }
        foreach ($allMovements as $row) {
            ChemicalMovement::create($row);
        }

        $this->info('Stocks in DB: '.ChemicalStock::count());
        $this->info('Movements in DB: '.ChemicalMovement::count());

        return self::SUCCESS;
    }

    private function parseWorkbook(string $path, string $location): array
    {
        $xls = IOFactory::load($path);
        $stocks = [];
        $movements = [];

        foreach ($xls->getWorksheetIterator() as $sheet) {
            $title = trim((string) $sheet->getTitle());
            $upper = strtoupper($title);

            if (str_contains($upper, 'VOUCHER')) {
                continue;
            }

            $rows = $sheet->toArray(null, true, true, false);
            if (count($rows) < 2) {
                continue;
            }

            if (str_contains($upper, 'CURRENT STOCK')) {
                $stocks = array_merge($stocks, $this->parseCurrentStock($rows, $location));
                continue;
            }

            if (str_contains($upper, 'INCOMING') || str_contains($upper, 'OUTGOING')) {
                $period = $this->periodFromSheetTitle($title);
                $movements = array_merge($movements, $this->parseMovements($rows, $location, $period));
            }
        }

        return compact('stocks', 'movements');
    }

    private function parseCurrentStock(array $rows, string $location): array
    {
        $map = null;
        $out = [];

        foreach ($rows as $row) {
            $cells = array_map(fn ($c) => trim((string) ($c ?? '')), $row);
            $first = strtoupper($cells[0] ?? '');

            // Detect / re-detect header blocks (sheet stacks multiple dated inventories)
            if ($first === 'ITEM') {
                $header = array_map(fn ($c) => strtoupper(trim((string) $c)), $row);
                $map = $this->headerMap($header, [
                    'item' => ['ITEM'],
                    'existing_count' => ['EXISTING COUNT', 'CURRENT COUNT'],
                    'count_date' => ['DATE OF EXISTING COUNT', 'DATE OF CURRENT COUNT'],
                    'counted_by' => ['COUNTED BY'],
                    'location_stored' => ['LOCATION STORED'],
                    'verified_by_photo' => ['VERIFIED BY PHOTO?', 'VERIFIED BY PHOTO'],
                ]);
                continue;
            }

            if ($map === null || $first === '') {
                continue;
            }

            $item = $this->cell($row, $map['item'] ?? null);
            if ($item === '' || strtoupper($item) === 'ITEM') {
                continue;
            }

            $out[] = [
                'location' => $location,
                'item' => $item,
                'existing_count' => (int) preg_replace('/[^\d\-]/', '', $this->cell($row, $map['existing_count'] ?? null) ?: '0'),
                'count_date' => $this->parseDate($this->cell($row, $map['count_date'] ?? null)),
                'counted_by' => $this->nullable($this->cell($row, $map['counted_by'] ?? null)),
                'location_stored' => $this->nullable($this->cell($row, $map['location_stored'] ?? null)) ?: $location,
                'verified_by_photo' => $this->nullable($this->cell($row, $map['verified_by_photo'] ?? null)),
            ];
        }

        return $out;
    }

    private function parseMovements(array $rows, string $location, string $period): array
    {
        $header = array_map(fn ($c) => strtoupper(trim((string) $c)), $rows[0] ?? []);
        $map = $this->headerMap($header, [
            'item' => ['ITEM'],
            'brand' => ['BRAND'],
            'size_or_pieces' => ['SIZE OR PIECES'],
            'movement_date' => ['DATE ADDED OR REMOVED'],
            'movement_type' => ['ADDED OR REMOVED?', 'ADDED OR REMOVED'],
            'quantity_text' => ['# ADDED OR REMOVED?', '# ADDED OR REMOVED'],
            'moved_by' => ['ADDED/REMOVED BY?', 'ADDED/REMOVED BY', 'ADDED\\REMOVED BY?'],
            'remaining_count' => ['REMAINING COUNT'],
        ]);

        // Fallback for ADDED/REMOVED BY if slash variants miss
        if (($map['moved_by'] ?? null) === null) {
            foreach ($header as $idx => $h) {
                if (str_contains($h, 'ADDED') && str_contains($h, 'BY')) {
                    $map['moved_by'] = $idx;
                    break;
                }
            }
        }

        $out = [];
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $item = $this->cell($row, $map['item'] ?? null);
            if ($item === '') {
                continue;
            }

            $qtyText = $this->cell($row, $map['quantity_text'] ?? null);
            $qty = null;
            if ($qtyText !== '' && preg_match('/(-?\d+)/', $qtyText, $m)) {
                $qty = (int) $m[1];
            }

            $type = strtoupper(trim($this->cell($row, $map['movement_type'] ?? null)));
            if ($type === '') {
                $type = null;
            }

            $remaining = $this->cell($row, $map['remaining_count'] ?? null);
            $remainingInt = $remaining === '' ? null : (int) preg_replace('/[^\d\-]/', '', $remaining);

            $out[] = [
                'location' => $location,
                'period_label' => $period,
                'item' => $item,
                'brand' => $this->nullable($this->cell($row, $map['brand'] ?? null)),
                'size_or_pieces' => $this->nullable($this->cell($row, $map['size_or_pieces'] ?? null)),
                'movement_date' => $this->parseDate($this->cell($row, $map['movement_date'] ?? null)),
                'movement_type' => $type,
                'quantity_text' => $this->nullable($qtyText),
                'quantity' => $qty,
                'moved_by' => $this->nullable($this->cell($row, $map['moved_by'] ?? null)),
                'remaining_count' => $remainingInt,
            ];
        }

        return $out;
    }

    private function periodFromSheetTitle(string $title): string
    {
        $upper = strtoupper($title);
        if (preg_match('/\b(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER)\b/', $upper, $m)) {
            return $m[1];
        }

        return trim(preg_replace('/[^A-Z0-9 ]+/', ' ', $upper));
    }

    private function headerMap(array $header, array $aliases): array
    {
        $map = [];
        foreach ($aliases as $key => $names) {
            $map[$key] = null;
            foreach ($names as $name) {
                $idx = array_search($name, $header, true);
                if ($idx !== false) {
                    $map[$key] = $idx;
                    break;
                }
            }
        }

        return $map;
    }

    private function cell(array $row, ?int $idx): string
    {
        if ($idx === null) {
            return '';
        }

        return trim((string) ($row[$idx] ?? ''));
    }

    private function nullable(string $value): ?string
    {
        return $value === '' ? null : $value;
    }

    private function parseDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable $e) {
                // fall through
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
