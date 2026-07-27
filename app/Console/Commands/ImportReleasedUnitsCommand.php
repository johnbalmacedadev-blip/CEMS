<?php

namespace App\Console\Commands;

use App\Models\Make;
use App\Models\Vehicle;
use App\Models\VehicleExpense;
use App\Models\VehicleModel;
use App\Models\VehicleStatusDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportReleasedUnitsCommand extends Command
{
    protected $signature = 'import:released-units
                            {--file= : Path to JSON export (default: storage/app/released_units_import.json)}
                            {--branch= : Optional branch/location name (e.g. Annex, Flagship) to set on vehicles}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import released units + vehicle expenses from released_units Excel JSON export';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/released_units_import.json');

        if (! is_file($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $rows = json_decode(file_get_contents($path), true);
        if (! is_array($rows) || $rows === []) {
            $this->error('JSON file is empty or invalid.');
            return self::FAILURE;
        }

        $branchId = null;
        $branchName = trim((string) $this->option('branch'));
        if ($branchName !== '') {
            $branch = \App\Models\BranchLocation::query()
                ->whereRaw('LOWER(name) = ?', [strtolower($branchName)])
                ->first();
            if (! $branch) {
                $this->error("Branch/location not found: {$branchName}");
                return self::FAILURE;
            }
            $branchId = $branch->id;
            $this->info("Branch location: {$branch->name} (id {$branchId})");
        }

        $this->info('Rows to process: '.count($rows));
        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');
        }

        $created = 0;
        $updated = 0;
        $expensesUpserted = 0;
        $statusUpserted = 0;
        $errors = 0;

        // Preload vehicles keyed by normalized plate
        $vehicleByPlate = [];
        foreach (Vehicle::query()->get(['id', 'plate_number']) as $vehicle) {
            $vehicleByPlate[$this->normalizePlate($vehicle->plate_number)] = $vehicle->id;
        }

        $makeCache = [];
        $modelCache = [];

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            try {
                if ($this->option('dry-run')) {
                    $bar->advance();
                    continue;
                }

                DB::transaction(function () use (
                    $row,
                    $branchId,
                    $branchName,
                    &$vehicleByPlate,
                    &$makeCache,
                    &$modelCache,
                    &$created,
                    &$updated,
                    &$expensesUpserted,
                    &$statusUpserted
                ) {
                    $plate = $this->normalizePlate($row['plate_number'] ?? '');
                    if ($plate === '') {
                        throw new \RuntimeException('Missing plate number');
                    }

                    [$makeId, $makeName] = $this->resolveMake($row['make'] ?? 'Unknown', $makeCache);
                    [$modelId, $modelName] = $this->resolveModel($makeId, $row['model'] ?? 'Unknown', $modelCache);

                    $payload = [
                        'year' => (int) ($row['year'] ?? 2000),
                        'make_id' => $makeId,
                        'model_id' => $modelId,
                        'make' => $makeName,
                        'model' => $modelName,
                        'variant' => $this->nullableString($row['variant'] ?? null),
                        'transmission' => $this->mapTransmission($row['transmission'] ?? null),
                        'fuel_type' => $this->mapFuel($row['fuel_type'] ?? null),
                        'kilometers' => (int) ($row['kilometers'] ?? 0),
                        'plate_number' => $plate,
                        'colour' => $this->nullableString($row['colour'] ?? null) ?: 'N/A',
                        'with_tools' => (bool) ($row['with_tools'] ?? false),
                        'with_matting' => (bool) ($row['with_matting'] ?? false),
                        'with_spare_tire' => (bool) ($row['with_spare_tire'] ?? false),
                        'purchase_price' => (float) ($row['purchase_price'] ?? 0),
                        'posted_price' => (float) ($row['posted_price'] ?? 0),
                        'sold_price' => (float) ($row['sales_price'] ?? 0),
                        'purchased_from' => $this->nullableString($row['purchased_from'] ?? null) ?: 'N/A',
                        'purchase_date' => $row['purchase_date'] ?? ($row['release_date'] ?? now()->toDateString()),
                        'spare_key' => (bool) ($row['spare_key'] ?? false),
                        'status' => 'Released',
                    ];

                    if ($branchId) {
                        $payload['branch_location_id'] = $branchId;
                    }

                    if (isset($vehicleByPlate[$plate])) {
                        $vehicle = Vehicle::findOrFail($vehicleByPlate[$plate]);
                        // Keep existing plate formatting if already in DB
                        unset($payload['plate_number']);
                        $vehicle->update($payload);
                        $updated++;
                    } else {
                        $vehicle = Vehicle::create($payload);
                        $vehicleByPlate[$plate] = $vehicle->id;
                        $created++;
                    }

                    $expensePayload = [
                        'paint_items' => $row['paint_items'] ?? null,
                        'paint_costs' => (float) ($row['paint_costs'] ?? 0),
                        'mechanical_electrical_items' => $row['mechanical_electrical_items'] ?? null,
                        'mechanical_electrical_costs' => (float) ($row['mechanical_electrical_costs'] ?? 0),
                        'cluster_items' => $row['cluster_items'] ?? null,
                        'cluster_costs' => (float) ($row['cluster_costs'] ?? 0),
                        'aircon_items' => $row['aircon_items'] ?? null,
                        'aircon_cost' => (float) ($row['aircon_cost'] ?? 0),
                        'interior_items' => $row['interior_items'] ?? null,
                        'interior_costs' => (float) ($row['interior_costs'] ?? 0),
                        'papers_items' => $row['papers_items'] ?? null,
                        'papers_costs' => (float) ($row['papers_costs'] ?? 0),
                        'tyres_battery_items' => $row['tyres_battery_items'] ?? null,
                        'tyres_battery_cost' => (float) ($row['tyres_battery_cost'] ?? 0),
                        'misc_items' => $row['misc_items'] ?? null,
                        'misc_costs' => (float) ($row['misc_costs'] ?? 0),
                        'total_repair_items' => $row['total_repair_items'] ?? null,
                        'total_repair_cost' => (float) ($row['total_repair_cost'] ?? 0),
                        'post_reservation_repairs' => $row['post_reservation_repairs'] ?? null,
                        'post_reservation_repairs_cost' => (float) ($row['post_reservation_repairs_cost'] ?? 0),
                        'total_capital_repair_capital_posted' => (float) ($row['total_capital_repair_capital_posted'] ?? 0),
                        'price' => (float) ($row['posted_price'] ?? 0),
                    ];

                    VehicleExpense::updateOrCreate(
                        ['plate_number' => $vehicle->plate_number],
                        $expensePayload
                    );
                    $expensesUpserted++;

                    [$cashFinancing, $financingCompany, $hasTradeIn] = $this->mapCashFinancing($row['cash_financing_raw'] ?? null);

                    $statusPayload = [
                        'showroom' => $this->nullableString($row['showroom'] ?? null) ?: ($branchName !== '' ? $branchName : null),
                        'sale_date' => $row['sale_date'] ?? null,
                        'sales_price' => (float) ($row['sales_price'] ?? 0),
                        'sale_reservation_amount' => (float) ($row['sale_reservation_amount'] ?? 0),
                        'sales_person_reserved' => $this->nullableString($row['sales_person_reserved'] ?? null),
                        'sales_person_release' => $this->nullableString($row['sales_person_release'] ?? null),
                        'good_sales_review' => $row['good_sales_review'] ?? null,
                        'cash_financing' => $cashFinancing,
                        'financing_company' => $financingCompany,
                        'sale_origin' => $this->nullableString($row['sale_origin'] ?? null),
                        'agent_cost' => (float) ($row['agent_cost'] ?? 0),
                        'finance_revenue_1' => (float) ($row['finance_revenue_1'] ?? 0),
                        'finance_revenue_2' => (float) ($row['finance_revenue_2'] ?? 0),
                        'sale_status' => 'Released',
                        'transfer_cost' => (float) ($row['transfer_cost'] ?? 0),
                        'release_date' => $row['release_date'] ?? null,
                        'days_from_reservation_to_release' => $row['days_from_reservation_to_release'] ?? null,
                        'has_insurance' => $this->mapInsurance($row['insurance_raw'] ?? null),
                        'has_trade_in' => $hasTradeIn,
                        'customer_first_name' => $this->nullableString($row['customer_first_name'] ?? null),
                        'customer_last_name' => $this->nullableString($row['customer_last_name'] ?? null),
                        'customer_date_of_birth' => $row['customer_date_of_birth'] ?? null,
                        'customer_gender' => $this->mapGender($row['customer_gender'] ?? null),
                        'customer_location' => $this->nullableString($row['customer_location'] ?? null),
                        'customer_purpose' => $this->nullableString($row['customer_purpose'] ?? null),
                    ];

                    VehicleStatusDetail::updateOrCreate(
                        ['plate_number' => $vehicle->plate_number],
                        $statusPayload
                    );
                    $statusUpserted++;
                });
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error('Row '.($row['row'] ?? '?').' plate '.($row['plate_number'] ?? '?').': '.$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created vehicles', $created],
                ['Updated vehicles', $updated],
                ['Expenses upserted', $expensesUpserted],
                ['Status details upserted', $statusUpserted],
                ['Errors', $errors],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $plate) ?? '');
    }

    private function nullableString($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function mapTransmission(?string $value): string
    {
        $v = strtoupper(trim((string) $value));
        if (str_contains($v, 'MAN')) {
            return 'Manual';
        }
        return 'Automatic';
    }

    private function mapFuel(?string $value): string
    {
        $v = strtoupper(trim((string) $value));
        if (str_contains($v, 'DIESEL') || $v === 'DSL') {
            return 'Diesel';
        }
        return 'Gasoline';
    }

    /**
     * @return array{0:?string,1:?string,2:bool}
     */
    private function mapCashFinancing(?string $raw): array
    {
        $v = strtoupper(trim((string) $raw));
        if ($v === '') {
            return [null, null, false];
        }

        $hasTradeIn = str_contains($v, 'TRADE');

        $companies = ['ASIALINK', 'JACCS', 'BERJAYA', 'ORICO', 'ORICIO'];
        foreach ($companies as $company) {
            if (str_contains($v, $company)) {
                $name = $company === 'ORICIO' ? 'Orico' : ucfirst(strtolower($company));
                if ($company === 'ASIALINK') {
                    $name = 'Asialink';
                } elseif ($company === 'JACCS') {
                    $name = 'Jaccs';
                } elseif ($company === 'BERJAYA') {
                    $name = 'Berjaya';
                } elseif (in_array($company, ['ORICO', 'ORICIO'], true)) {
                    $name = 'Orico';
                }

                return ['Financing', $name, $hasTradeIn];
            }
        }

        if (str_contains($v, 'FINANC')) {
            return ['Financing', null, $hasTradeIn];
        }

        return ['Cash', null, $hasTradeIn];
    }

    private function mapInsurance(?string $raw): bool
    {
        $v = strtoupper(trim((string) $raw));
        if ($v === '') {
            return false;
        }
        if (str_contains($v, 'WITHOUT') || str_contains($v, 'NO ')) {
            return false;
        }
        return str_contains($v, 'WITH') || str_contains($v, 'YES');
    }

    private function mapGender(?string $raw): ?string
    {
        $v = strtoupper(trim((string) $raw));
        if ($v === '' || in_array($v, ['N/A', 'NA', '-', 'NONE', 'NULL'], true)) {
            return null;
        }

        // Couple / joint buyers
        if (
            str_contains($v, '/')
            || str_contains($v, 'AND')
            || str_contains($v, 'BOTH')
            || str_contains($v, 'M/F')
            || str_contains($v, 'F/M')
            || (str_contains($v, 'MALE') && str_contains($v, 'FEMALE'))
        ) {
            return 'Other';
        }

        if (str_contains($v, 'FEM') || $v === 'F' || $v === 'SHE') {
            return 'Female';
        }

        if (str_contains($v, 'MAL') || $v === 'M' || $v === 'HE') {
            return 'Male';
        }

        // Place names / junk in gender column
        return 'Other';
    }

    /**
     * @param  array<string, array{0:int,1:string}>  $cache
     * @return array{0:int,1:string}
     */
    private function resolveMake(string $name, array &$cache): array
    {
        $key = strtoupper(trim($name));
        if ($key === '') {
            $key = 'UNKNOWN';
            $name = 'Unknown';
        }
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $make = Make::query()
            ->whereRaw('UPPER(name) = ?', [$key])
            ->first();

        if (! $make) {
            $make = Make::create([
                'name' => ucwords(strtolower($name)),
                'is_active' => true,
            ]);
        }

        $cache[$key] = [$make->id, $make->name];

        return $cache[$key];
    }

    /**
     * @param  array<string, array{0:int,1:string}>  $cache
     * @return array{0:int,1:string}
     */
    private function resolveModel(int $makeId, string $name, array &$cache): array
    {
        $key = $makeId.'|'.strtoupper(trim($name));
        if (trim($name) === '') {
            $name = 'Unknown';
            $key = $makeId.'|UNKNOWN';
        }
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $model = VehicleModel::query()
            ->where('make_id', $makeId)
            ->whereRaw('UPPER(name) = ?', [strtoupper(trim($name))])
            ->first();

        if (! $model) {
            $model = VehicleModel::create([
                'make_id' => $makeId,
                'name' => ucwords(strtolower($name)),
                'is_active' => true,
            ]);
        }

        $cache[$key] = [$model->id, $model->name];

        return $cache[$key];
    }
}
