<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GasExpense;
use App\Models\Vehicle;

class GasExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some vehicles to add gas expenses to
        $vehicles = Vehicle::take(5)->get();
        
        if ($vehicles->count() > 0) {
            $gasExpenses = [
                [
                    'date' => '2025-05-24',
                    'driver' => 'ARGIE',
                    'model' => 'MAZDA CX-5',
                    'gas_amount' => 498.00,
                    'expense_sent_by' => 'MERLIN',
                    'has_photo_video_in_groupchat' => true,
                    'photo_fuel_gauge_before' => true,
                    'photo_fuel_gauge_after' => true,
                    'photo_car_license_plate_gas_boy' => true,
                    'photo_receipt_next_to_gas_pump' => true,
                    'checked_by' => 'MARJ',
                ],
                [
                    'date' => '2025-05-24',
                    'driver' => 'JIM T',
                    'model' => 'FORD TERRITORY',
                    'gas_amount' => 499.00,
                    'expense_sent_by' => 'MERLIN',
                    'has_photo_video_in_groupchat' => true,
                    'photo_fuel_gauge_before' => true,
                    'photo_fuel_gauge_after' => true,
                    'photo_car_license_plate_gas_boy' => true,
                    'photo_receipt_next_to_gas_pump' => true,
                    'checked_by' => 'MARJ',
                ],
                [
                    'date' => '2025-05-25',
                    'driver' => 'OLIVER',
                    'model' => 'TOYOTA RUSH',
                    'gas_amount' => 495.00,
                    'expense_sent_by' => 'MERLIN',
                    'has_photo_video_in_groupchat' => true,
                    'photo_fuel_gauge_before' => true,
                    'photo_fuel_gauge_after' => true,
                    'photo_car_license_plate_gas_boy' => true,
                    'photo_receipt_next_to_gas_pump' => true,
                    'checked_by' => 'MARJ',
                ],
                [
                    'date' => '2025-05-25',
                    'driver' => 'MIKE C',
                    'model' => 'HONDA BRIO',
                    'gas_amount' => 496.00,
                    'expense_sent_by' => 'MERLIN',
                    'has_photo_video_in_groupchat' => true,
                    'photo_fuel_gauge_before' => true,
                    'photo_fuel_gauge_after' => true,
                    'photo_car_license_plate_gas_boy' => true,
                    'photo_receipt_next_to_gas_pump' => true,
                    'checked_by' => 'MARJ',
                ],
                [
                    'date' => '2025-05-25',
                    'driver' => 'MARIANO',
                    'model' => '17 SUZUKI SWIFT',
                    'gas_amount' => 998.00,
                    'expense_sent_by' => 'MERLIN',
                    'has_photo_video_in_groupchat' => true,
                    'photo_fuel_gauge_before' => true,
                    'photo_fuel_gauge_after' => true,
                    'photo_car_license_plate_gas_boy' => true,
                    'photo_receipt_next_to_gas_pump' => true,
                    'checked_by' => 'MARJ',
                ],
            ];
            
            foreach ($vehicles as $index => $vehicle) {
                if (isset($gasExpenses[$index])) {
                    $gasExpenseData = $gasExpenses[$index];
                    $gasExpenseData['plate_number'] = $vehicle->plate_number;
                    
                    GasExpense::create($gasExpenseData);
                }
            }
        }
    }
}
