<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class ImportPurchasePricesCommand extends Command
{
    protected $signature = 'import:purchase-prices
                            {--file= : Path to PRIVATE PURCHASE PRICES.xlsx}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Update vehicle purchase_price (+ totals) from PRIVATE PURCHASE PRICES workbook for matching plates only';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/PRIVATE_PURCHASE_PRICES.xlsx');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $jsonPath = storage_path('app/purchase_prices_import.json');
        $script = base_path('scripts/export_purchase_prices.py');
        if (! is_file($script)) {
            $this->error("Exporter not found: {$script}");

            return self::FAILURE;
        }

        $this->info('Exporting Excel via Python…');
        $result = Process::timeout(300)->env([
            'PURCHASE_PRICES_SRC' => $path,
            'PURCHASE_PRICES_OUT' => $jsonPath,
        ])->run(['python', $script]);

        if ($result->exitCode() !== 0) {
            $this->error(trim($result->errorOutput() ?: $result->output() ?: 'Python export failed'));

            return self::FAILURE;
        }
        $this->line(trim($result->output()));

        $payload = json_decode((string) file_get_contents($jsonPath), true);
        if (! is_array($payload)) {
            $this->error('Invalid JSON export.');

            return self::FAILURE;
        }

        $prices = $payload['purchase_prices'] ?? [];
        $financials = $payload['financials'] ?? [];
        $this->info('Excel purchase prices: '.count($prices).' | financial rows: '.count($financials));

        $vehicleByPlate = [];
        foreach (Vehicle::query()->get(['id', 'plate_number', 'purchase_price', 'total_costs', 'total_revenue', 'total_profit']) as $vehicle) {
            $key = $this->normalizePlate($vehicle->plate_number);
            if ($key !== '') {
                $vehicleByPlate[$key] = $vehicle;
            }
        }

        $priceUpdated = 0;
        $priceUnchanged = 0;
        $priceMissing = 0;
        $finUpdated = 0;
        $finMissing = 0;

        if ($this->option('dry-run')) {
            foreach ($prices as $row) {
                $plate = $this->normalizePlate($row['plate'] ?? '');
                if ($plate === '' || ! isset($vehicleByPlate[$plate])) {
                    $priceMissing++;
                    continue;
                }
                $new = round((float) ($row['purchase_price'] ?? 0), 2);
                $old = round((float) ($vehicleByPlate[$plate]->purchase_price ?? 0), 2);
                if ($old !== $new) {
                    $priceUpdated++;
                } else {
                    $priceUnchanged++;
                }
            }
            foreach ($financials as $row) {
                $plate = $this->normalizePlate($row['plate'] ?? '');
                if ($plate === '' || ! isset($vehicleByPlate[$plate])) {
                    $finMissing++;
                } else {
                    $finUpdated++;
                }
            }
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Purchase prices would update', $priceUpdated],
                    ['Purchase prices already same', $priceUnchanged],
                    ['Purchase prices no matching vehicle', $priceMissing],
                    ['Financial rows matching vehicle', $finUpdated],
                    ['Financial rows no matching vehicle', $finMissing],
                ]
            );
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        foreach ($prices as $row) {
            $plate = $this->normalizePlate($row['plate'] ?? '');
            if ($plate === '' || ! isset($vehicleByPlate[$plate])) {
                $priceMissing++;
                continue;
            }
            $vehicle = Vehicle::find($vehicleByPlate[$plate]->id);
            if (! $vehicle) {
                $priceMissing++;
                continue;
            }
            $new = round((float) ($row['purchase_price'] ?? 0), 2);
            $old = round((float) ($vehicle->purchase_price ?? 0), 2);
            if ($old === $new) {
                $priceUnchanged++;
                continue;
            }
            $vehicle->purchase_price = $new;
            $vehicle->save();
            $priceUpdated++;
        }

        foreach ($financials as $row) {
            $plate = $this->normalizePlate($row['plate'] ?? '');
            if ($plate === '' || ! isset($vehicleByPlate[$plate])) {
                $finMissing++;
                continue;
            }
            $vehicle = Vehicle::find($vehicleByPlate[$plate]->id);
            if (! $vehicle) {
                $finMissing++;
                continue;
            }
            $vehicle->total_costs = $row['total_costs'] !== null ? round((float) $row['total_costs'], 2) : $vehicle->total_costs;
            $vehicle->total_revenue = $row['total_revenue'] !== null ? round((float) $row['total_revenue'], 2) : $vehicle->total_revenue;
            $vehicle->total_profit = $row['total_profit'] !== null ? round((float) $row['total_profit'], 2) : $vehicle->total_profit;
            // Prefer dedicated PURCHASE PRICES sheet; only fill purchase from financials if still empty/zero.
            if ((float) ($vehicle->purchase_price ?? 0) <= 0 && isset($row['purchase_price']) && $row['purchase_price'] !== null) {
                $vehicle->purchase_price = round((float) $row['purchase_price'], 2);
            }
            $vehicle->save();
            $finUpdated++;
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Purchase prices updated', $priceUpdated],
                ['Purchase prices unchanged', $priceUnchanged],
                ['Purchase prices no matching vehicle', $priceMissing],
                ['Financial totals updated', $finUpdated],
                ['Financial rows no matching vehicle', $finMissing],
            ]
        );

        return self::SUCCESS;
    }

    private function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $plate) ?? '');
    }
}
