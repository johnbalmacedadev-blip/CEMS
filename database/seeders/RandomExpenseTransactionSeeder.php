<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseTransaction;
use App\Models\ExpenseItem;
use App\Models\Vehicle;
use App\Models\PaymentMethod;
use Carbon\Carbon;

class RandomExpenseTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all vehicles and payment methods
        $vehicles = Vehicle::all();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        
        if ($vehicles->isEmpty()) {
            $this->command->warn('No vehicles found in database. Please add vehicles first.');
            return;
        }
        
        if ($paymentMethods->isEmpty()) {
            $this->command->warn('No payment methods found in database. Please run PaymentMethodSeeder first.');
            return;
        }
        
        // Define the 3 dates
        $dates = [
            Carbon::create(2025, 11, 3),
            Carbon::create(2025, 11, 8),
            Carbon::create(2025, 11, 11),
        ];
        
        // Sample expense descriptions for Vehicle type
        $vehicleExpenseDescriptions = [
            'Battery', 'Paint', 'Cluster', 'Paper', 'Tyers', 'Tires', 'Brake Pads', 
            'Oil Change', 'Engine Repair', 'Transmission Service', 'AC Repair',
            'Wheel Alignment', 'Suspension', 'Exhaust System', 'Radiator',
            'Alternator', 'Starter Motor', 'Fuel Pump', 'Spark Plugs', 'Air Filter'
        ];
        
        // Sample expense descriptions for Operating type
        $operatingExpenseDescriptions = [
            'Office Supplies', 'Utilities', 'Internet Bill', 'Phone Bill',
            'Rent', 'Insurance', 'Marketing', 'Advertising', 'Legal Fees',
            'Accounting Services', 'Software License', 'Equipment Maintenance',
            'Cleaning Services', 'Security Services', 'Office Furniture',
            'Stationery', 'Printing', 'Postage', 'Travel Expenses', 'Meals'
        ];
        
        // Sample names for requested_by, approved_by, care_of
        $names = ['John', 'Jane', 'Mike', 'Sarah', 'David', 'Emily', 'Chris', 'Lisa', 
                  'JAY', 'jhong', 'Ltms', 'Robert', 'Maria', 'James', 'Patricia'];
        
        // Sample store names
        $stores = ['Auto Parts Store', 'Service Center', 'Dealership', 'Online Shop',
                   'Hardware Store', 'Office Depot', 'Somewhere', 'Main Shop',
                   'Branch Store', 'Warehouse', 'Retail Outlet'];
        
        $this->command->info('Generating 100 random expense transactions...');
        
        for ($i = 1; $i <= 100; $i++) {
            // Randomly select a date
            $transactionDate = $dates[array_rand($dates)];
            
            // Random starting cash and added cash
            $startingCash = rand(10000, 100000);
            $addedCash = rand(0, 50000);
            $totalCash = $startingCash + $addedCash;
            
            // Create transaction
            $transaction = ExpenseTransaction::create([
                'transaction_date' => $transactionDate,
                'starting_cash' => $startingCash,
                'added_cash' => $addedCash,
                'total_cash' => $totalCash,
                'total_expense' => 0, // Will be calculated after items are created
                'cash_remaining' => $totalCash,
            ]);
            
            // Create 1-3 expense items per transaction
            $numItems = rand(1, 3);
            $totalExpense = 0;
            
            for ($j = 1; $j <= $numItems; $j++) {
                // Randomly decide if Vehicle or Operating
                $isVehicle = rand(0, 1) === 1;
                $paymentTag = $isVehicle ? 'Vehicle' : 'Operating';
                
                // Select random vehicle (only if Vehicle type)
                $vehicle = null;
                if ($isVehicle && $vehicles->isNotEmpty()) {
                    $vehicle = $vehicles->random();
                }
                
                // Select random payment method
                $paymentMethod = $paymentMethods->random();
                
                // Select random description
                $description = $isVehicle 
                    ? $vehicleExpenseDescriptions[array_rand($vehicleExpenseDescriptions)]
                    : $operatingExpenseDescriptions[array_rand($operatingExpenseDescriptions)];
                
                // Random cost
                $cost = rand(200, 10000);
                $totalExpense += $cost;
                
                // Random expense date (same as transaction date or within 3 days before)
                $expenseDate = $transactionDate->copy()->subDays(rand(0, 3));
                
                // Create expense item
                ExpenseItem::create([
                    'expense_transaction_id' => $transaction->id,
                    'expense_date' => $expenseDate,
                    'payment_method_id' => $paymentMethod->id,
                    'description' => $description,
                    'description_details' => rand(0, 1) ? 'Additional notes for ' . $description : null,
                    'care_of' => rand(0, 1) ? $names[array_rand($names)] : null,
                    'requested_by' => rand(0, 1) ? $names[array_rand($names)] : null,
                    'approved_by' => rand(0, 1) ? $names[array_rand($names)] : null,
                    'store_shop' => rand(0, 1) ? $stores[array_rand($stores)] : null,
                    'cost' => $cost,
                    'payment_tag' => $paymentTag,
                    'vehicle_id' => $vehicle ? $vehicle->id : null,
                    'receipt_checked' => rand(0, 1) === 1,
                    'receipt_checker' => rand(0, 1) ? $names[array_rand($names)] : null,
                    'receipt_check_date' => rand(0, 1) ? $expenseDate->copy()->addDays(rand(1, 5)) : null,
                ]);
            }
            
            // Update transaction totals
            $cashRemaining = $totalCash - $totalExpense;
            $transaction->update([
                'total_expense' => $totalExpense,
                'cash_remaining' => $cashRemaining,
            ]);
            
            if ($i % 10 === 0) {
                $this->command->info("Created {$i} transactions...");
            }
        }
        
        $this->command->info('Successfully generated 100 random expense transactions!');
    }
}
