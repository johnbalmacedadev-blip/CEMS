<?php

use App\Models\Make;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\VehicleRegistration;
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
    ['date' => '2026-06-01', 'plate' => 'NDX9237', 'year' => 2016, 'make' => 'MITSUBISHI', 'series' => 'ASXGLS', 'renewal_reg_or' => 2310, 'renewal_sop' => 400, 'smoke_na' => 1000, 'remarks' => 'C/O ALYSSA', 'coc_no' => 'COC 14380952', 'status' => null],
    ['date' => '2026-06-01', 'plate' => 'DAM7195', 'year' => 2019, 'make' => 'HONDA', 'series' => 'HR-V', 'renewal_reg_or' => 2010, 'renewal_sop' => 400, 'smoke_na' => 1000, 'remarks' => 'C/O GOTCHI', 'coc_no' => 'COC 14380953', 'status' => null],
    ['date' => '2026-06-01', 'plate' => 'ABE8872', 'year' => 2016, 'make' => 'HONDA', 'series' => 'CITY', 'renewal_reg_or' => null, 'renewal_sop' => null, 'smoke_na' => null, 'remarks' => 'C/O JESON', 'coc_no' => 'COC 14380954', 'status' => 'DONE NA FEB. 6'],
    ['date' => '2026-06-01', 'plate' => 'NEV2292', 'year' => 2021, 'make' => 'GEELY', 'series' => 'SX11', 'renewal_reg_or' => null, 'renewal_sop' => null, 'smoke_na' => null, 'remarks' => 'C/O JESON', 'coc_no' => 'COC 14380955', 'status' => 'DONE NA FEB. 6'],
    ['date' => '2026-06-01', 'plate' => 'NAL4293', 'year' => 2017, 'make' => 'ISUZU', 'series' => 'MU-X', 'renewal_reg_or' => 3460, 'renewal_sop' => 400, 'smoke_na' => 1000, 'remarks' => 'C/O RIZALYN', 'coc_no' => 'COC 14380956', 'status' => null],
    ['date' => '2026-06-01', 'plate' => 'NGD2213', 'year' => 2020, 'make' => 'MG', 'series' => 'MG5', 'renewal_reg_or' => 9010, 'renewal_sop' => 400, 'smoke_na' => 1000, 'remarks' => 'C/O JM', 'coc_no' => 'COC 14380957', 'status' => null],
    ['date' => '2026-06-01', 'plate' => 'NBM2940', 'year' => 2018, 'make' => 'FORD', 'series' => 'EVEREST', 'renewal_reg_or' => 3600, 'renewal_sop' => 400, 'smoke_na' => 1000, 'remarks' => 'C/O RON', 'coc_no' => 'COC 14380958', 'status' => null],
    ['date' => '2026-06-02', 'plate' => 'NBH7267', 'year' => 2020, 'make' => 'TOYOTA', 'series' => 'FORTUNER', 'renewal_reg_or' => 2310, 'renewal_sop' => 400, 'smoke_na' => 1000, 'remarks' => 'C/O ALYSSA', 'coc_no' => 'COC 14380959', 'status' => null],
    ['date' => '2026-06-03', 'plate' => 'NBP3993', 'year' => 2019, 'make' => 'NISSAN', 'series' => 'TERRA', 'renewal_reg_or' => 3460, 'renewal_sop' => 400, 'smoke_na' => 1000, 'remarks' => 'C/O JIMMY', 'coc_no' => 'COC 14380960', 'status' => null],
    ['date' => '2026-06-03', 'plate' => 'NDZ3557', 'year' => 2016, 'make' => 'MITSUBISHI', 'series' => 'MIRAGE', 'renewal_reg_or' => 1610, 'renewal_sop' => 400, 'smoke_na' => 1000, 'remarks' => 'JACCS', 'coc_no' => 'COC 14380961', 'status' => null],
    ['date' => '2026-06-03', 'plate' => 'NCM1678', 'year' => 2018, 'make' => 'CHEVROLET', 'series' => 'SPARK', 'renewal_reg_or' => 1610, 'renewal_sop' => 400, 'smoke_na' => 1000, 'remarks' => 'ASIALINK', 'coc_no' => 'COC 14380962', 'status' => null],
    ['date' => '2026-06-04', 'plate' => 'NGB6986', 'year' => 2021, 'make' => 'ISUZU', 'series' => 'MU-X', 'renewal_reg_or' => null, 'renewal_sop' => null, 'smoke_na' => null, 'remarks' => 'C/O JIMMY', 'coc_no' => 'COC 14380963', 'status' => null],
    ['date' => '2026-06-04', 'plate' => 'NIT8207', 'year' => 2023, 'make' => 'HONDA', 'series' => 'BRIO', 'renewal_reg_or' => 1610, 'renewal_sop' => 400, 'smoke_na' => 1000, 'remarks' => 'C/O RON', 'coc_no' => 'COC 14380964', 'status' => null],
    ['date' => '2026-06-04', 'plate' => 'NAR8307', 'year' => 2017, 'make' => 'TOYOTA', 'series' => 'HI ACE S GRANDIA', 'renewal_reg_or' => null, 'renewal_sop' => null, 'smoke_na' => null, 'remarks' => 'ORICO', 'coc_no' => 'COC 14380965', 'status' => null],
    ['date' => '2026-06-04', 'plate' => 'NBJ3903', 'year' => 2018, 'make' => 'NISSAN', 'series' => 'JUKE', 'renewal_reg_or' => null, 'renewal_sop' => null, 'smoke_na' => null, 'remarks' => 'C/O THYRA', 'coc_no' => 'COC 14380966', 'status' => null],
];

$vehiclesCreated = 0;
$created = 0;
$skipped = 0;
$errors = [];

foreach ($records as $row) {
    try {
        $existsVehicle = !Vehicle::whereRaw('UPPER(REPLACE(plate_number, " ", "")) = ?', [strtoupper(str_replace(' ', '', $row['plate']))])->exists();
        $vehicle = findOrCreateVehicle($row['plate'], $row['year'], $row['make'], $row['series']);
        if (!$existsVehicle) {
            $vehiclesCreated++;
        }

        if (VehicleRegistration::where('vehicle_id', $vehicle->id)->where('date', $row['date'])->exists()) {
            $skipped++;
            continue;
        }

        VehicleRegistration::create([
            'date' => $row['date'],
            'vehicle_id' => $vehicle->id,
            'renewal_reg_or' => $row['renewal_reg_or'],
            'renewal_sop' => $row['renewal_sop'],
            'smoke_na' => $row['smoke_na'],
            'duplicate_plate' => null,
            'migrate' => null,
            'duplicate_cr' => null,
            'pnp_clearance' => null,
            'confirmation' => null,
            'remarks' => $row['remarks'],
            'coc_no' => $row['coc_no'],
            'status' => $row['status'],
        ]);
        $created++;
    } catch (Throwable $e) {
        $errors[] = $row['plate'] . ': ' . $e->getMessage();
    }
}

echo "Vehicles created: $vehiclesCreated\n";
echo "Registration records created: $created\n";
echo "Skipped (duplicate date+vehicle): $skipped\n";
echo "Total vehicle_registrations: " . VehicleRegistration::count() . "\n";
if ($errors) {
    echo "Errors:\n" . implode("\n", $errors) . "\n";
}
