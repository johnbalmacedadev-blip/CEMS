<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Make;
use App\Models\VehicleModel;

class ModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelsData = [
            'Toyota' => [
                'Camry', 'Corolla', 'RAV4', 'Highlander', 'Prius', 'Avalon', 'Sienna', 
                'Tacoma', 'Tundra', '4Runner', 'Sequoia', 'Land Cruiser', 'Yaris', 
                'C-HR', 'Venza', 'Innova', 'Fortuner', 'Hilux', 'Alphard', 'Vios'
            ],
            'Honda' => [
                'Civic', 'Accord', 'CR-V', 'Pilot', 'HR-V', 'Passport', 'Ridgeline', 
                'Insight', 'Fit', 'Odyssey', 'Element', 'S2000', 'NSX', 'Prelude', 
                'Integra', 'City', 'BR-V', 'Mobilio', 'Brio', 'Freed'
            ],
            'Nissan' => [
                'Altima', 'Sentra', 'Rogue', 'Murano', 'Pathfinder', 'Armada', 
                'Frontier', 'Titan', '370Z', 'GT-R', 'Leaf', 'Versa', 'Maxima', 
                'Juke', 'Cube', 'Navara', 'Terra', 'Almera', 'Livina', 'Grand Livina'
            ],
            'Mitsubishi' => [
                'Outlander', 'Eclipse Cross', 'Mirage', 'Lancer', 'Galant', 
                'Diamante', 'Montero', 'Endeavor', 'Eclipse', '3000GT', 'Starion', 
                'Cordia', 'Tredia', 'Sigma', 'Debonair', 'Strada', 'Adventure', 
                'Xpander', 'Mirage G4', 'ASX'
            ],
            'Suzuki' => [
                'Swift', 'SX4', 'Grand Vitara', 'Kizashi', 'Equator', 'XL7', 
                'Aerio', 'Esteem', 'Forenza', 'Reno', 'Verona', 'X-90', 'Samurai', 
                'Sidekick', 'Vitara', 'Ertiga', 'Celerio', 'Jimny', 'Baleno', 'Dzire'
            ],
            'Mazda' => [
                'Mazda3', 'Mazda6', 'CX-5', 'CX-9', 'MX-5', 'RX-8', 'Tribute', 
                'B-Series', 'MPV', 'Protege', 'Millenia', '929', 'RX-7', 'Cosmo', 
                'Eunos', 'CX-3', 'CX-30', 'MX-30', 'BT-50', 'Mazda2'
            ],
            'Hyundai' => [
                'Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Palisade', 'Veloster', 
                'Genesis', 'Accent', 'Azera', 'Entourage', 'Tiburon', 'XG300', 
                'XG350', 'Scoupe', 'Excel', 'Creta', 'Venue', 'Kona', 'i10', 'i20'
            ],
            'Kia' => [
                'Forte', 'Optima', 'Sportage', 'Sorento', 'Telluride', 'Soul', 
                'Stinger', 'Cadenza', 'Sedona', 'Spectra', 'Amanti', 'Rondo', 
                'Borrego', 'Rio', 'Niro', 'Picanto', 'Carens', 'Carnival', 'Seltos', 'Stonic'
            ],
            'Ford' => [
                'Focus', 'Fusion', 'Escape', 'Explorer', 'Expedition', 'F-150', 
                'Ranger', 'Mustang', 'Edge', 'Flex', 'Taurus', 'Crown Victoria', 
                'Thunderbird', 'GT', 'Bronco', 'Everest', 'Territory', 'EcoSport', 'Fiesta', 'Mondeo'
            ],
            'Chevrolet' => [
                'Cruze', 'Malibu', 'Equinox', 'Traverse', 'Tahoe', 'Silverado', 
                'Colorado', 'Camaro', 'Corvette', 'Impala', 'Avalanche', 'Suburban', 
                'Blazer', 'Tracker', 'Cavalier', 'Sonic', 'Spark', 'Trax', 'Trailblazer', 'Optra'
            ],
            'BMW' => [
                '3 Series', '5 Series', '7 Series', 'X1', 'X3', 'X5', 'X7', 
                'Z4', 'i3', 'i8', 'M3', 'M5', 'X6', '2 Series', '4 Series', 
                '6 Series', '8 Series', 'iX', 'X2', 'X4'
            ],
            'Mercedes-Benz' => [
                'C-Class', 'E-Class', 'S-Class', 'A-Class', 'B-Class', 'GLA', 
                'GLC', 'GLE', 'GLS', 'G-Class', 'CLS', 'SL', 'AMG GT', 'Sprinter', 
                'V-Class', 'CLA', 'GLB', 'EQC', 'A-Class Sedan', 'GLE Coupe'
            ],
            'Audi' => [
                'A3', 'A4', 'A6', 'A8', 'Q3', 'Q5', 'Q7', 'Q8', 'TT', 'R8', 
                'e-tron', 'A1', 'A5', 'A7', 'Q2', 'RS3', 'RS4', 'RS6', 'S3', 'S4'
            ],
            'Volkswagen' => [
                'Golf', 'Jetta', 'Passat', 'Tiguan', 'Atlas', 'Beetle', 'CC', 
                'Touareg', 'Polo', 'Vento', 'Virtus', 'Taos', 'ID.4', 'Arteon', 
                'T-Cross', 'T-Roc', 'Caddy', 'Transporter', 'Crafter', 'Amarok'
            ],
            'Lexus' => [
                'ES', 'IS', 'GS', 'LS', 'RX', 'GX', 'LX', 'NX', 'UX', 'LC', 
                'RC', 'CT', 'HS', 'SC', 'LFA', 'ES Hybrid', 'RX Hybrid', 'NX Hybrid', 'UX Hybrid', 'LS Hybrid'
            ],
        ];

        foreach ($modelsData as $makeName => $models) {
            $make = Make::where('name', $makeName)->first();
            
            if ($make) {
                foreach ($models as $modelName) {
                    VehicleModel::create([
                        'make_id' => $make->id,
                        'name' => $modelName,
                        'description' => $makeName . ' ' . $modelName . ' model',
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}