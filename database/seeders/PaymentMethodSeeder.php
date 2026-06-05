<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            ['name' => 'COST CENTER (FLAGSHIP BUDGET)', 'sort_order' => 1],
            ['name' => 'COST CENTER (WAREHOUSE BUDGET)', 'sort_order' => 2],
            ['name' => 'COST CENTER (ANNEX BUDGET)', 'sort_order' => 3],
            ['name' => 'GCASH', 'sort_order' => 4],
            ['name' => 'CREDIT CARD #1', 'sort_order' => 5],
            ['name' => 'CREDIT CARD #2', 'sort_order' => 6],
            ['name' => 'CREDIT CARD #3', 'sort_order' => 7],
            ['name' => 'CREDIT CARD #4', 'sort_order' => 8],
            ['name' => 'CREDIT CARD #5', 'sort_order' => 9],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                [
                    'is_active' => true,
                    'sort_order' => $method['sort_order'],
                ]
            );
        }
    }
}
