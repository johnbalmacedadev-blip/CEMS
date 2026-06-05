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

$branch = BranchLocation::whereRaw('LOWER(name) = ?', ['flagship'])->firstOrFail();
$records = require __DIR__ . '/data/transfer_orcr_flagship_may_2026.php';

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
            'remark' => $row['remark'] ?? null,
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
            'remarks' => $row['remarks'] ?? null,
            'release_date' => $row['release_date'] ?? null,
            'status' => TransferOrcr::STATUS_DONE,
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
echo "Flagship DONE (May 2026): " . TransferOrcr::where('branch_location_id', $branch->id)->where('status', TransferOrcr::STATUS_DONE)->whereBetween('date', ['2026-05-01', '2026-05-31'])->count() . "\n";
echo "Total transfer_orcr: " . TransferOrcr::count() . "\n";
if ($errors) {
    echo "Errors:\n" . implode("\n", $errors) . "\n";
}
