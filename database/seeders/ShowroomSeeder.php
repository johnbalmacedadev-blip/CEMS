<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Showroom;

class ShowroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $showrooms = [
            [
                'name' => 'FLAGSHIP',
                'description' => 'Flagship Showroom',
                'is_active' => true,
            ],
            [
                'name' => 'PREMIUM',
                'description' => 'Premium Showroom',
                'is_active' => true,
            ],
        ];

        foreach ($showrooms as $showroom) {
            Showroom::updateOrCreate(
                ['name' => $showroom['name']],
                $showroom
            );
        }
    }
}
