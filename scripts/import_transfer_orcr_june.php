<?php

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
    return Make::firstOrCreate(
        ['name' => $name],
        ['is_active' => true]
    );
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
        'plate_number' => $plate,
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

$records = [
    ['date' => '2026-06-01', 'plate' => 'NEC3350', 'year' => 2019, 'make' => 'NISSAN', 'series' => 'ALMERA', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'LTO MUNTINLUPA', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'PAID', 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NDX3237', 'year' => 2016, 'make' => 'MITSUBISHI', 'series' => 'ASX GLS', 'transaction_type' => 'ASIALINK', 'remarks' => null, 'lto_file_no' => 'TAGUIG', 'transfer_sop' => 1000, 'transfer_or' => 450, 'others' => 150, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => null, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'transfer_sop_paid' => true, 'transfer_or_paid' => true, 'pnp_clearance_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NIX2274', 'year' => 2016, 'make' => 'FORD', 'series' => 'EVEREST', 'transaction_type' => 'ASIALINK', 'remarks' => null, 'lto_file_no' => 'MUNTINLUPA', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => 500, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD QUEZON CITY', 'rd_sop' => 800, 'rd_or' => 1000, 'pnp_clearance_paid' => true, 'notary_paid' => true, 'confirmation_paid' => true, 'rd_sop_paid' => true, 'rd_or_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NIG1772', 'year' => 2023, 'make' => 'TOYOTA', 'series' => 'RAZE', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'VALENZUELA', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true, 'confirmation_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NCD3300', 'year' => 2013, 'make' => 'MAZDA', 'series' => 'MAZDA 2', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'MUNTINLUPA', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true, 'confirmation_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NAQ3861', 'year' => 2017, 'make' => 'HYUNDAI', 'series' => 'ACCENT', 'transaction_type' => 'ASIALINK', 'remarks' => 'OTHERS: PARAÑAQUE', 'lto_file_no' => 'NCR', 'transfer_sop' => 1000, 'transfer_or' => 230, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => null, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'transfer_sop_paid' => true, 'transfer_or_paid' => true, 'pnp_clearance_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NAQ843', 'year' => 2019, 'make' => 'TOYOTA', 'series' => 'RUSH', 'transaction_type' => 'BERJAYA', 'remarks' => 'OTHERS: PARAÑAQUE', 'lto_file_no' => 'NCR', 'transfer_sop' => 1000, 'transfer_or' => 230, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => null, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'transfer_sop_paid' => true, 'transfer_or_paid' => true, 'pnp_clearance_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NGD2270', 'year' => 2020, 'make' => 'MG', 'series' => 'MG5', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'MUNTINLUPA', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD QUEZON CITY', 'rd_sop' => 800, 'rd_or' => 800, 'pnp_clearance_paid' => true, 'confirmation_paid' => true, 'rd_sop_paid' => true, 'rd_or_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'ABF8372', 'year' => 2015, 'make' => 'HONDA', 'series' => 'CITY', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'VALENZUELA', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD CALAMBA', 'rd_sop' => 1500, 'rd_or' => 800, 'pnp_clearance_paid' => true, 'confirmation_paid' => true, 'rd_sop_paid' => true, 'rd_or_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NIB1072', 'year' => 2024, 'make' => 'TOYOTA', 'series' => 'VELOZ', 'transaction_type' => 'ASIALINK', 'remarks' => null, 'lto_file_no' => 'LAS PIÑAS', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => 500, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true, 'notary_paid' => true, 'confirmation_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NAF2571', 'year' => 2021, 'make' => 'GEELY', 'series' => 'COOLRAY', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'MUNTINLUPA', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD LAS PIÑAS', 'rd_sop' => 500, 'rd_or' => 800, 'pnp_clearance_paid' => true, 'confirmation_paid' => true, 'rd_sop_paid' => true, 'rd_or_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'DAC4173', 'year' => 2020, 'make' => 'TOYOTA', 'series' => 'ALTIS', 'transaction_type' => 'RUSH', 'remarks' => null, 'lto_file_no' => 'BACOOR', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => 500, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD STA CRUZ LAGUNA', 'rd_sop' => 2500, 'rd_or' => 500, 'pnp_clearance_paid' => true, 'notary_paid' => true, 'confirmation_paid' => true, 'rd_sop_paid' => true, 'rd_or_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NDD4513', 'year' => 2017, 'make' => 'HONDA', 'series' => 'BR-V', 'transaction_type' => null, 'remarks' => 'VALID ID', 'lto_file_no' => 'MEYCAUAYAN', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true, 'confirmation_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NHJ4358', 'year' => 2025, 'make' => 'TOYOTA', 'series' => 'AVANZA', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'MUNTINLUPA', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true, 'confirmation_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NIE5776', 'year' => 2024, 'make' => 'NISSAN', 'series' => 'TERRA', 'transaction_type' => 'ASIALINK', 'remarks' => null, 'lto_file_no' => 'MUNTINLUPA', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => 500, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true, 'notary_paid' => true, 'confirmation_paid' => true],
    ['date' => '2026-06-01', 'plate' => 'NEY2212', 'year' => 2021, 'make' => 'GEELY', 'series' => 'COOLRAY', 'transaction_type' => 'ORICO', 'remarks' => null, 'lto_file_no' => 'LAGUNA', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD TAGUIG', 'rd_sop' => 1500, 'rd_or' => 800, 'pnp_clearance_paid' => true, 'confirmation_paid' => true, 'rd_sop_paid' => true, 'rd_or_paid' => true],
    ['date' => '2026-06-03', 'plate' => 'NIK1188', 'year' => 2016, 'make' => 'FORD', 'series' => 'EXPLORER', 'transaction_type' => 'ASIALINK', 'remarks' => 'OTHERS: PARAÑAQUE', 'lto_file_no' => 'NCR', 'transfer_sop' => 1000, 'transfer_or' => 430, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'transfer_sop_paid' => true, 'transfer_or_paid' => true, 'pnp_clearance_paid' => true, 'confirmation_paid' => true],
    ['date' => '2026-06-03', 'plate' => 'NAN3114', 'year' => 2020, 'make' => 'FORD', 'series' => 'RANGER', 'transaction_type' => 'ASIALINK', 'remarks' => null, 'lto_file_no' => 'LAS PIÑAS', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => 500, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true, 'notary_paid' => true, 'confirmation_paid' => true],
    ['date' => '2026-06-03', 'plate' => 'NBR4241', 'year' => 2018, 'make' => 'FORD', 'series' => 'EVEREST', 'transaction_type' => null, 'remarks' => 'OTHERS: PARAÑAQUE', 'lto_file_no' => 'NCR', 'transfer_sop' => 1000, 'transfer_or' => 230, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => null, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'transfer_sop_paid' => true, 'transfer_or_paid' => true, 'pnp_clearance_paid' => true],
    ['date' => '2026-06-03', 'plate' => 'NIV4340', 'year' => 2024, 'make' => 'TOYOTA', 'series' => 'YARIS CROSS', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'NCR', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => null, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true],
    ['date' => '2026-06-04', 'plate' => 'NIF3217', 'year' => 2023, 'make' => 'HONDA', 'series' => 'BR-V', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'NCR', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => null, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true],
    ['date' => '2026-06-04', 'plate' => 'NDD7332', 'year' => 2017, 'make' => 'HONDA', 'series' => 'BR-V', 'transaction_type' => 'ORICO', 'remarks' => null, 'lto_file_no' => 'SAN RAFAEL', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true, 'confirmation_paid' => true],
    ['date' => '2026-06-04', 'plate' => 'DAR1797', 'year' => 2021, 'make' => 'HONDA', 'series' => 'CITY', 'transaction_type' => 'ORICO', 'remarks' => null, 'lto_file_no' => 'NCR', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => 500, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true, 'notary_paid' => true, 'confirmation_paid' => true],
    ['date' => '2026-06-04', 'plate' => 'DAT8345', 'year' => 2021, 'make' => 'TOYOTA', 'series' => 'RUSH', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'SAN PEDRO', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => 'RD TRECE', 'rd_sop' => 500, 'rd_or' => 1000, 'pnp_clearance_paid' => true, 'confirmation_paid' => true, 'rd_sop_paid' => true, 'rd_or_paid' => true],
    ['date' => '2026-06-04', 'plate' => 'DAN1103', 'year' => 2019, 'make' => 'TOYOTA', 'series' => 'VIOS', 'transaction_type' => null, 'remarks' => null, 'lto_file_no' => 'CABUYAO', 'transfer_sop' => 0, 'transfer_or' => 0, 'others' => null, 'notary' => null, 'pnp_clearance' => 2500, 'confirmation' => 500, 'rd' => null, 'rd_sop' => null, 'rd_or' => null, 'pnp_clearance_paid' => true, 'confirmation_paid' => true],
];

$vehiclesCreated = 0;
$created = 0;
$skipped = 0;
$errors = [];

foreach ($records as $row) {
    try {
        $existingVehicle = Vehicle::whereRaw('UPPER(REPLACE(plate_number, " ", "")) = ?', [strtoupper(str_replace(' ', '', $row['plate']))])->exists();
        $vehicle = findOrCreateVehicle($row['plate'], $row['year'], $row['make'], $row['series']);
        if (!$existingVehicle) {
            $vehiclesCreated++;
        }

        $exists = TransferOrcr::where('vehicle_id', $vehicle->id)
            ->where('date', $row['date'])
            ->exists();
        if ($exists) {
            $skipped++;
            continue;
        }

        $transactionType = trim($row['transaction_type'] ?? '') ?: 'ORCR';

        TransferOrcr::create([
            'date' => $row['date'],
            'vehicle_id' => $vehicle->id,
            'transaction_type' => $transactionType,
            'lto_file_no' => $row['lto_file_no'],
            'transfer_sop' => $row['transfer_sop'] ?? 0,
            'transfer_or' => $row['transfer_or'] ?? 0,
            'others' => $row['others'],
            'notary' => $row['notary'],
            'pnp_clearance' => $row['pnp_clearance'] ?? 0,
            'confirmation' => $row['confirmation'],
            'rd' => $row['rd'],
            'rd_sop' => $row['rd_sop'],
            'rd_or' => $row['rd_or'],
            'remarks' => $row['remarks'],
            'status' => 'Pending',
            'transfer_sop_paid' => $row['transfer_sop_paid'] ?? (($row['transfer_sop'] ?? 0) > 0),
            'transfer_or_paid' => $row['transfer_or_paid'] ?? (($row['transfer_or'] ?? 0) > 0),
            'pnp_clearance_paid' => $row['pnp_clearance_paid'] ?? (($row['pnp_clearance'] ?? 0) > 0),
            'rd_sop_paid' => $row['rd_sop_paid'] ?? (($row['rd_sop'] ?? 0) > 0),
            'rd_or_paid' => $row['rd_or_paid'] ?? (($row['rd_or'] ?? 0) > 0),
        ]);
        $created++;
    } catch (Throwable $e) {
        $errors[] = $row['plate'] . ': ' . $e->getMessage();
    }
}

echo "Vehicles created: $vehiclesCreated\n";
echo "Transfer ORCR records created: $created\n";
echo "Skipped (duplicate): $skipped\n";
echo "Total transfer_orcr now: " . TransferOrcr::count() . "\n";
if ($errors) {
    echo "Errors:\n" . implode("\n", $errors) . "\n";
}
