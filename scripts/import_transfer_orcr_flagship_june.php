<?php

use App\Models\BranchLocation;
use App\Models\Make;
use App\Models\TransferOrcr;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

function findOrCreateMake(string $name): Make
{
    return Make::firstOrCreate(['name' => $name], ['is_active' => true]);
}

function findOrCreateModel(Make $make, string $name): VehicleModel
{
    return VehicleModel::firstOrCreate(
        ['make_id' => $make->id, 'name' => $name],
        ['is_active' => true]
    );
}

function findOrCreateVehicle(string $plate, int $year, string $makeName, string $seriesName): Vehicle
{
    $normalized = strtoupper(str_replace(' ', '', $plate));
    $vehicle = Vehicle::whereRaw('UPPER(REPLACE(plate_number, " ", "")) = ?', [$normalized])->first();
    if ($vehicle) {
        return $vehicle;
    }

    $make = findOrCreateMake($makeName);
    $model = findOrCreateModel($make, $seriesName);

    return Vehicle::create([
        'year' => $year,
        'make_id' => $make->id,
        'model_id' => $model->id,
        'make' => $makeName,
        'model' => $seriesName,
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'kilometers' => 0,
        'plate_number' => strtoupper($plate),
        'colour' => 'N/A',
        'with_tools' => false,
        'with_matting' => false,
        'with_spare_tire' => false,
        'purchase_price' => 0,
        'purchased_from' => 'Import',
        'purchase_date' => now()->toDateString(),
        'spare_key' => false,
        'status' => 'Available',
    ]);
}

$branch = BranchLocation::whereRaw('LOWER(name) = ?', ['flagship'])->first();
if (!$branch) {
    $branch = BranchLocation::create([
        'name' => 'Flagship',
        'description' => 'Flagship branch / store',
        'is_active' => true,
        'sort_order' => 1,
    ]);
}

$records = [
    ['date' => '2026-06-01', 'plate' => 'NAC6401', 'year' => 2017, 'make' => 'HYUNDAI', 'series' => 'ACCENT', 'transaction_type' => 'ASIALINK', 'remark' => null, 'lto_file_no' => 'NCR', 'transfer_sop' => 1500, 'transfer_or' => 230, 'others_note' => 'PARAÑAQUE', 'pnp_clearance' => 2500],
    ['date' => '2026-06-01', 'plate' => 'NAD1900', 'year' => 2018, 'make' => 'TOYOTA', 'series' => 'RUSH', 'transaction_type' => 'BERJAYA', 'remark' => null, 'lto_file_no' => 'NCR', 'transfer_sop' => 1500, 'transfer_or' => 230, 'others_note' => 'PARAÑAQUE', 'pnp_clearance' => 2500],
    ['date' => '2026-06-01', 'plate' => 'NGD2213', 'year' => 2020, 'make' => 'MG', 'series' => 'MG5', 'transaction_type' => null, 'remark' => null, 'lto_file_no' => 'MUNTINLUPA', 'transfer_sop' => 0, 'transfer_or' => 0, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD QUEZON CITY', 'rd_sop' => 600, 'rd_or' => 800],
    ['date' => '2026-06-01', 'plate' => 'ABE8072', 'year' => 2014, 'make' => 'HONDA', 'series' => 'CITY', 'transaction_type' => null, 'remark' => null, 'lto_file_no' => 'VALENZUELA', 'pnp_clearance' => 2500, 'rd' => 'RD CALAMBA', 'rd_sop' => 1500, 'rd_or' => 800],
    ['date' => '2026-06-01', 'plate' => 'NH51022', 'year' => 2024, 'make' => 'TOYOTA', 'series' => 'VELOZ', 'transaction_type' => 'ASIALINK', 'remark' => null, 'lto_file_no' => 'LAS PIÑAS', 'pnp_clearance' => 2500, 'confirmation' => 500, 'notary' => 500],
    ['date' => '2026-06-01', 'plate' => 'NGP3711', 'year' => 2021, 'make' => 'GEELY', 'series' => 'COOLRAY', 'transaction_type' => null, 'remark' => null, 'lto_file_no' => 'MUNTINLUPA', 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD LAS PIÑAS', 'rd_sop' => 500, 'rd_or' => 800],
    ['date' => '2026-06-01', 'plate' => 'DAD4479', 'year' => 2020, 'make' => 'TOYOTA', 'series' => 'ALTIS', 'transaction_type' => 'RUSH', 'remark' => null, 'lto_file_no' => 'BACOOR', 'pnp_clearance' => 2500, 'confirmation' => 500, 'notary' => 500, 'rd' => 'RD STA CRUZ LAGUNA', 'rd_sop' => 3000, 'rd_or' => 900],
    ['date' => '2026-06-01', 'plate' => 'NBP4918', 'year' => 2017, 'make' => 'HONDA', 'series' => 'BR-V', 'transaction_type' => null, 'remark' => 'VALID ID', 'lto_file_no' => 'MEYCAUAYAN', 'pnp_clearance' => 2500, 'confirmation' => 500],
    ['date' => '2026-06-01', 'plate' => 'NHJ4933', 'year' => 2025, 'make' => 'TOYOTA', 'series' => 'AVANZA', 'transaction_type' => null, 'remark' => null, 'lto_file_no' => 'MUNTINLUPA', 'pnp_clearance' => 2500, 'confirmation' => 500],
    ['date' => '2026-06-01', 'plate' => 'NIQ5726', 'year' => 2024, 'make' => 'NISSAN', 'series' => 'TERRA', 'transaction_type' => 'ASIALINK', 'remark' => null, 'lto_file_no' => 'MUNTINLUPA', 'pnp_clearance' => 2500, 'confirmation' => 500, 'notary' => 500],
    ['date' => '2026-06-01', 'plate' => 'NEV2232', 'year' => 2021, 'make' => 'GEELY', 'series' => 'COOLRAY', 'transaction_type' => 'ORICO', 'remark' => null, 'lto_file_no' => 'LA LOMA', 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD TAGUIG', 'rd_sop' => 800, 'rd_or' => 1500],
    ['date' => '2026-06-03', 'plate' => 'NKW88', 'year' => 2016, 'make' => 'FORD', 'series' => 'EXPLORER', 'transaction_type' => 'ASIALINK', 'remark' => null, 'lto_file_no' => 'NCR', 'transfer_sop' => 1500, 'transfer_or' => 430, 'others_note' => 'PARAÑAQUE', 'pnp_clearance' => 2500],
    ['date' => '2026-06-03', 'plate' => 'NAN9114', 'year' => 2020, 'make' => 'FORD', 'series' => 'RANGER', 'transaction_type' => 'ASIALINK', 'remark' => null, 'lto_file_no' => 'LAS PIÑAS', 'pnp_clearance' => 2500, 'confirmation' => 500, 'notary' => 500],
    ['date' => '2026-06-03', 'plate' => 'NBM2940', 'year' => 2018, 'make' => 'FORD', 'series' => 'EVEREST', 'transaction_type' => null, 'remark' => null, 'lto_file_no' => 'NCR', 'transfer_sop' => 1500, 'transfer_or' => 230, 'others_note' => 'PARAÑAQUE', 'pnp_clearance' => 2500],
    ['date' => '2026-06-03', 'plate' => 'NNV4040', 'year' => 2024, 'make' => 'TOYOTA', 'series' => 'YARIS CROSS', 'transaction_type' => null, 'remark' => null, 'lto_file_no' => 'NCR', 'pnp_clearance' => 2500],
    ['date' => '2026-06-04', 'plate' => 'IAF3247', 'year' => 2023, 'make' => 'HONDA', 'series' => 'BR-V', 'transaction_type' => null, 'remark' => null, 'lto_file_no' => 'NCR', 'pnp_clearance' => 2500],
    ['date' => '2026-06-04', 'plate' => 'NBB7092', 'year' => 2017, 'make' => 'HONDA', 'series' => 'BR-V', 'transaction_type' => 'ORICO', 'remark' => null, 'lto_file_no' => 'SAN RAFAEL', 'pnp_clearance' => 2500, 'confirmation' => 500],
    ['date' => '2026-06-04', 'plate' => 'DAU6397', 'year' => 2021, 'make' => 'HONDA', 'series' => 'CITY', 'transaction_type' => 'ORICO', 'remark' => null, 'lto_file_no' => 'MUNTINLUPA', 'pnp_clearance' => 2500, 'confirmation' => 500, 'notary' => 500],
    ['date' => '2026-06-04', 'plate' => 'DAT5248', 'year' => 2021, 'make' => 'TOYOTA', 'series' => 'RUSH', 'transaction_type' => null, 'remark' => null, 'lto_file_no' => 'SAN PEDRO', 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD TRECE', 'rd_sop' => 500, 'rd_or' => 1000],
    ['date' => '2026-06-04', 'plate' => 'DAM8163', 'year' => 2019, 'make' => 'TOYOTA', 'series' => 'WIGO', 'transaction_type' => null, 'remark' => null, 'lto_file_no' => 'CABUYAO', 'pnp_clearance' => 2500, 'confirmation' => 500],
    ['date' => '2026-06-04', 'plate' => 'NBH7267', 'year' => 2020, 'make' => 'TOYOTA', 'series' => 'FORTUNER', 'transaction_type' => null, 'remark' => null, 'lto_file_no' => 'VALENZUELA', 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD PAO'],
];

$vehiclesCreated = 0;
$created = 0;
$updated = 0;
$errors = [];

foreach ($records as $row) {
    try {
        $normalized = strtoupper(str_replace(' ', '', $row['plate']));
        $hadVehicle = Vehicle::whereRaw('UPPER(REPLACE(plate_number, " ", "")) = ?', [$normalized])->exists();
        $vehicle = findOrCreateVehicle($row['plate'], $row['year'], $row['make'], $row['series']);
        if (!$hadVehicle) {
            $vehiclesCreated++;
        }

        $transferSop = $row['transfer_sop'] ?? 0;
        $transferOr = $row['transfer_or'] ?? 0;
        $pnp = $row['pnp_clearance'] ?? 0;
        $confirmation = $row['confirmation'] ?? null;
        $notary = $row['notary'] ?? null;
        $rdSop = $row['rd_sop'] ?? null;
        $rdOr = $row['rd_or'] ?? null;

        $payload = [
            'branch_location_id' => $branch->id,
            'date' => $row['date'],
            'vehicle_id' => $vehicle->id,
            'transaction_type' => trim($row['transaction_type'] ?? '') ?: 'ORCR',
            'remark' => $row['remark'],
            'lto_file_no' => $row['lto_file_no'],
            'transfer_sop' => $transferSop,
            'transfer_or' => $transferOr,
            'others' => null,
            'others_note' => $row['others_note'] ?? null,
            'notary' => $notary,
            'pnp_clearance' => $pnp,
            'confirmation' => $confirmation,
            'rd' => $row['rd'] ?? null,
            'rd_sop' => $rdSop,
            'rd_or' => $rdOr,
            'remarks' => null,
            'status' => TransferOrcr::STATUS_PENDING,
            'transfer_sop_paid' => $transferSop > 0,
            'transfer_or_paid' => $transferOr > 0,
            'pnp_clearance_paid' => $pnp > 0,
            'rd_sop_paid' => ($rdSop ?? 0) > 0,
            'rd_or_paid' => ($rdOr ?? 0) > 0,
        ];

        $existing = TransferOrcr::where('vehicle_id', $vehicle->id)
            ->where('date', $row['date'])
            ->first();

        if ($existing) {
            $existing->update($payload);
            $updated++;
        } else {
            TransferOrcr::create($payload);
            $created++;
        }
    } catch (Throwable $e) {
        $errors[] = $row['plate'] . ': ' . $e->getMessage();
    }
}

echo "Branch: {$branch->name} (id {$branch->id})\n";
echo "Vehicles created: $vehiclesCreated\n";
echo "Transfer OR/CR created: $created\n";
echo "Transfer OR/CR updated: $updated\n";
echo "Flagship records (June 2026): " . TransferOrcr::where('branch_location_id', $branch->id)->whereBetween('date', ['2026-06-01', '2026-06-30'])->count() . "\n";
echo "Total transfer_orcr: " . TransferOrcr::count() . "\n";
if ($errors) {
    echo "Errors:\n" . implode("\n", $errors) . "\n";
}
