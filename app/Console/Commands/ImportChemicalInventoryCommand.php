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
                            {--movements-only : Only import INCOMING/OUTGOING (skip CURRENT STOCK)}
                            {--skip-existing : Skip movements/stocks that already exist (default when not --fresh)}
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

        $movementsOnly = (bool) $this->option('movements-only');
        $skipExisting = (bool) $this->option('skip-existing') || ! $this->option('fresh');

        if ($movementsOnly) {
            $allStocks = [];
            $this->info('Movements-only mode: CURRENT STOCK sheets ignored.');
        }

        $this->info('Total stocks: '.count($allStocks));
        $this->info('Total movements: '.count($allMovements));

        if ($this->option('dry-run')) {
            if ($skipExisting && ! $this->option('fresh')) {
                $existingKeys = $this->existingMovementKeys();
                $newCount = 0;
                foreach ($allMovements as $row) {
                    if (! isset($existingKeys[$this->movementKey($row)])) {
                        $newCount++;
                    }
                }
                $this->info("Movements that would be created (missing only): {$newCount}");
            }
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            ChemicalStock::query()->truncate();
            ChemicalMovement::query()->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->warn('chemical_stocks + chemical_movements truncated.');
            $skipExisting = false;
        }

        $stocksCreated = 0;
        $stocksSkipped = 0;
        $existingStockKeys = $skipExisting ? $this->existingStockKeys() : [];
        foreach ($allStocks as $row) {
            if ($skipExisting && isset($existingStockKeys[$this->stockKey($row)])) {
                $stocksSkipped++;
                continue;
            }
            $this->saveWithManualId(new ChemicalStock($row), ((int) ChemicalStock::query()->max('id')) + 1);
            $stocksCreated++;
            if ($skipExisting) {
                $existingStockKeys[$this->stockKey($row)] = true;
            }
        }

        $movementsCreated = 0;
        $movementsSkipped = 0;
        $existingMovementKeys = $skipExisting ? $this->existingMovementKeys() : [];
        foreach ($allMovements as $row) {
            $key = $this->movementKey($row);
            if ($skipExisting && isset($existingMovementKeys[$key])) {
                $movementsSkipped++;
                continue;
            }
            $this->saveWithManualId(new ChemicalMovement($row), ((int) ChemicalMovement::query()->max('id')) + 1);
            $movementsCreated++;
            $existingMovementKeys[$key] = true;
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Stocks created', $stocksCreated],
                ['Stocks skipped (existing)', $stocksSkipped],
                ['Movements created', $movementsCreated],
                ['Movements skipped (existing)', $movementsSkipped],
                ['Stocks in DB', ChemicalStock::count()],
                ['Movements in DB', ChemicalMovement::count()],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function movementKey(array $row): string
    {
        return strtoupper(implode('|', [
            (string) ($row['location'] ?? ''),
            (string) ($row['period_label'] ?? ''),
            (string) ($row['item'] ?? ''),
            (string) ($row['brand'] ?? ''),
            (string) ($row['size_or_pieces'] ?? ''),
            (string) ($row['movement_date'] ?? ''),
            (string) ($row['movement_type'] ?? ''),
            (string) ($row['quantity'] ?? $row['quantity_text'] ?? ''),
            (string) ($row['moved_by'] ?? ''),
            (string) ($row['remaining_count'] ?? ''),
        ]));
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function stockKey(array $row): string
    {
        return strtoupper(implode('|', [
            (string) ($row['location'] ?? ''),
            (string) ($row['item'] ?? ''),
            (string) ($row['count_date'] ?? ''),
            (string) ($row['existing_count'] ?? ''),
        ]));
    }

    /** @return array<string, true> */
    private function existingMovementKeys(): array
    {
        $keys = [];
        foreach (ChemicalMovement::query()->get() as $row) {
            $keys[$this->movementKey([
                'location' => $row->location,
                'period_label' => $row->period_label,
                'item' => $row->item,
                'brand' => $row->brand,
                'size_or_pieces' => $row->size_or_pieces,
                'movement_date' => optional($row->movement_date)->format('Y-m-d'),
                'movement_type' => $row->movement_type,
                'quantity' => $row->quantity,
                'quantity_text' => $row->quantity_text,
                'moved_by' => $row->moved_by,
                'remaining_count' => $row->remaining_count,
            ])] = true;
        }

        return $keys;
    }

    /** @return array<string, true> */
    private function existingStockKeys(): array
    {
        $keys = [];
        foreach (ChemicalStock::query()->get(['location', 'item', 'count_date', 'existing_count']) as $row) {
            $keys[$this->stockKey([
                'location' => $row->location,
                'item' => $row->item,
                'count_date' => optional($row->count_date)->format('Y-m-d'),
                'existing_count' => $row->existing_count,
            ])] = true;
        }

        return $keys;
    }

    private function saveWithManualId(\Illuminate\Database\Eloquent\Model $model, int $id): void
    {
        $model->id = $id;
        $model->incrementing = false;
        $model->save();
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
