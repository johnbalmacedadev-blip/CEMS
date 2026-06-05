<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use Carbon\Carbon;

class AdditionalVehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = $this->generateAdditionalVehicles(100);

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }

    private function generateAdditionalVehicles($count)
    {
        $makes = ['Toyota', 'Honda', 'Nissan', 'Mitsubishi', 'Suzuki', 'Mazda', 'Hyundai', 'Kia', 'Ford', 'Chevrolet'];
        
        $models = [
            'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Highlander', 'Prius', 'Avalon', 'Sienna', 'Tacoma', 'Tundra', '4Runner', 'Sequoia', 'Land Cruiser', 'Yaris', 'C-HR', 'Venza'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'HR-V', 'Passport', 'Ridgeline', 'Insight', 'Fit', 'Odyssey', 'Element', 'S2000', 'NSX', 'Prelude', 'Integra'],
            'Nissan' => ['Altima', 'Sentra', 'Rogue', 'Murano', 'Pathfinder', 'Armada', 'Frontier', 'Titan', '370Z', 'GT-R', 'Leaf', 'Versa', 'Maxima', 'Juke', 'Cube'],
            'Mitsubishi' => ['Outlander', 'Eclipse Cross', 'Mirage', 'Lancer', 'Galant', 'Diamante', 'Montero', 'Endeavor', 'Eclipse', '3000GT', 'Starion', 'Cordia', 'Tredia', 'Sigma', 'Debonair'],
            'Suzuki' => ['Swift', 'SX4', 'Grand Vitara', 'Kizashi', 'Equator', 'XL7', 'Aerio', 'Esteem', 'Forenza', 'Reno', 'Verona', 'X-90', 'Samurai', 'Sidekick', 'Vitara'],
            'Mazda' => ['Mazda3', 'Mazda6', 'CX-5', 'CX-9', 'MX-5', 'RX-8', 'Tribute', 'B-Series', 'MPV', 'Protege', 'Millenia', '929', 'RX-7', 'Cosmo', 'Eunos'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Palisade', 'Veloster', 'Genesis', 'Accent', 'Azera', 'Entourage', 'Tiburon', 'XG300', 'XG350', 'Scoupe', 'Excel'],
            'Kia' => ['Forte', 'Optima', 'Sportage', 'Sorento', 'Telluride', 'Soul', 'Stinger', 'Cadenza', 'Sedona', 'Spectra', 'Amanti', 'Rondo', 'Borrego', 'Rio', 'Niro'],
            'Ford' => ['Focus', 'Fusion', 'Escape', 'Explorer', 'Expedition', 'F-150', 'Ranger', 'Mustang', 'Edge', 'Flex', 'Taurus', 'Crown Victoria', 'Thunderbird', 'GT', 'Bronco'],
            'Chevrolet' => ['Cruze', 'Malibu', 'Equinox', 'Traverse', 'Tahoe', 'Silverado', 'Colorado', 'Camaro', 'Corvette', 'Impala', 'Avalanche', 'Suburban', 'Blazer', 'Tracker', 'Cavalier']
        ];

        $variants = ['Base', 'GL', 'GLX', 'Sport', 'Premium', 'Limited', 'Touring', 'SE', 'LE', 'XLE', 'EX', 'EX-L', 'LX', 'DX', 'Type R'];
        $transmissions = ['Manual', 'Automatic'];
        $fuelTypes = ['Gasoline', 'Diesel'];
        $colors = ['Black', 'White', 'Silver', 'Gray', 'Red', 'Blue', 'Green', 'Yellow', 'Orange', 'Brown', 'Gold', 'Maroon', 'Purple', 'Pink', 'Beige'];
        $statuses = ['Available', 'Sold', 'Maintenance'];
        
        $sellers = [
            'John Smith', 'Maria Garcia', 'Robert Johnson', 'Lisa Brown', 'Michael Davis', 'Sarah Wilson', 'David Martinez', 'Jennifer Anderson', 'Christopher Taylor', 'Amanda Thomas',
            'James Jackson', 'Michelle White', 'Daniel Harris', 'Ashley Martin', 'Matthew Thompson', 'Jessica Garcia', 'Andrew Martinez', 'Stephanie Robinson', 'Joshua Clark', 'Nicole Rodriguez',
            'Ryan Lewis', 'Samantha Lee', 'Brandon Walker', 'Megan Hall', 'Justin Allen', 'Rachel Young', 'Tyler Hernandez', 'Lauren King', 'Jacob Wright', 'Kayla Lopez',
            'Nathan Hill', 'Brittany Scott', 'Zachary Green', 'Danielle Adams', 'Caleb Baker', 'Victoria Gonzalez', 'Ethan Nelson', 'Amber Carter', 'Noah Mitchell', 'Sierra Perez',
            'Logan Roberts', 'Destiny Turner', 'Mason Phillips', 'Jasmine Campbell', 'Lucas Parker', 'Alexis Evans', 'Owen Edwards', 'Taylor Collins', 'Connor Stewart', 'Morgan Sanchez'
        ];

        $vehicles = [];
        $platePrefixes = ['DEF', 'GHI', 'JKL', 'MNO', 'PQR', 'STU', 'VWX', 'YZA', 'BCD', 'EFG', 'HIJ', 'KLM', 'NOP', 'QRS', 'TUV'];
        
        for ($i = 0; $i < $count; $i++) {
            $make = $makes[array_rand($makes)];
            $model = $models[$make][array_rand($models[$make])];
            $year = rand(2010, 2024);
            $kilometers = rand(10000, 300000);
            $plateNumber = $platePrefixes[array_rand($platePrefixes)] . '-' . rand(1000, 9999);
            
            // Ensure unique plate numbers
            while (Vehicle::where('plate_number', $plateNumber)->exists()) {
                $plateNumber = $platePrefixes[array_rand($platePrefixes)] . '-' . rand(1000, 9999);
            }
            
            $purchasePrice = rand(500000, 5000000);
            $purchaseDate = Carbon::now()->subDays(rand(1, 1825)); // Random date within last 5 years
            
            $vehicles[] = [
                'year' => $year,
                'make' => $make,
                'model' => $model,
                'variant' => $variants[array_rand($variants)],
                'transmission' => $transmissions[array_rand($transmissions)],
                'fuel_type' => $fuelTypes[array_rand($fuelTypes)],
                'kilometers' => $kilometers,
                'plate_number' => $plateNumber,
                'colour' => $colors[array_rand($colors)],
                'with_tools' => rand(0, 1) == 1,
                'with_matting' => rand(0, 1) == 1,
                'with_spare_tire' => rand(0, 1) == 1,
                'purchase_price' => $purchasePrice,
                'purchased_from' => $sellers[array_rand($sellers)],
                'purchase_date' => $purchaseDate,
                'spare_key' => rand(0, 1) == 1,
                'status' => $statuses[array_rand($statuses)],
                'notes' => rand(0, 1) == 1 ? 'Well maintained vehicle' : null,
            ];
        }
        
        return $vehicles;
    }
}