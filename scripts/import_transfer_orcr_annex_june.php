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

$branch = BranchLocation::whereRaw('LOWER(name) = ?', ['annex'])->first();
if (!$branch) {
    $branch = BranchLocation::create([
        'name' => 'Annex',
        'description' => 'Annex branch / store',
        'is_active' => true,
        'sort_order' => 2,
    ]);
}

$row = [
    'date' => '2026-06-03',
    'plate' => 'NFO7564',
    'year' => 2022,
    'make' => 'MG',
    'series' => 'ZS',
    'transaction_type' => 'BERJAYA',
    'remark' => null,
    'lto_file_no' => 'NCR',
    'transfer_sop' => 0,
    'transfer_or' => 0,
    'pnp_clearance' => 2500,
];

$vehicle = findOrCreateVehicle($row['plate'], $row['year'], $row['make'], $row['series']);

$payload = [
    'branch_location_id' => $branch->id,
    'date' => $row['date'],
    'vehicle_id' => $vehicle->id,
    'transaction_type' => $row['transaction_type'],
    'remark' => $row['remark'],
    'lto_file_no' => $row['lto_file_no'],
    'transfer_sop' => 0,
    'transfer_or' => 0,
    'others' => null,
    'others_note' => null,
    'notary' => null,
    'pnp_clearance' => $row['pnp_clearance'],
    'confirmation' => null,
    'rd' => null,
    'rd_sop' => null,
    'rd_or' => null,
    'remarks' => null,
    'status' => TransferOrcr::STATUS_PENDING,
    'transfer_sop_paid' => false,
    'transfer_or_paid' => false,
    'pnp_clearance_paid' => true,
    'rd_sop_paid' => false,
    'rd_or_paid' => false,
];

$existing = TransferOrcr::where('vehicle_id', $vehicle->id)
    ->where('date', $row['date'])
    ->first();

if ($existing) {
    $existing->update($payload);
    echo "Updated existing record for {$row['plate']} on {$row['date']} (Annex).\n";
} else {
    TransferOrcr::create($payload);
    echo "Created record for {$row['plate']} on {$row['date']} (Annex).\n";
}

echo "Branch: {$branch->name} (id {$branch->id})\n";
echo "Total Annex transfer_orcr: " . TransferOrcr::where('branch_location_id', $branch->id)->count() . "\n";
