<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Make;

class MakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $makes = [
            [
                'name' => 'Toyota',
                'description' => 'Japanese automotive manufacturer known for reliability and quality.',
                'is_active' => true,
            ],
            [
                'name' => 'Honda',
                'description' => 'Japanese multinational corporation known for automobiles and motorcycles.',
                'is_active' => true,
            ],
            [
                'name' => 'Nissan',
                'description' => 'Japanese multinational automobile manufacturer.',
                'is_active' => true,
            ],
            [
                'name' => 'Mitsubishi',
                'description' => 'Japanese multinational automotive manufacturer.',
                'is_active' => true,
            ],
            [
                'name' => 'Suzuki',
                'description' => 'Japanese multinational corporation specializing in automobiles and motorcycles.',
                'is_active' => true,
            ],
            [
                'name' => 'Mazda',
                'description' => 'Japanese multinational automaker based in Hiroshima.',
                'is_active' => true,
            ],
            [
                'name' => 'Hyundai',
                'description' => 'South Korean multinational automotive manufacturer.',
                'is_active' => true,
            ],
            [
                'name' => 'Kia',
                'description' => 'South Korean multinational automotive manufacturer.',
                'is_active' => true,
            ],
            [
                'name' => 'Ford',
                'description' => 'American multinational automobile manufacturer.',
                'is_active' => true,
            ],
            [
                'name' => 'Chevrolet',
                'description' => 'American automobile division of General Motors.',
                'is_active' => true,
            ],
            [
                'name' => 'BMW',
                'description' => 'German multinational corporation which produces automobiles and motorcycles.',
                'is_active' => true,
            ],
            [
                'name' => 'Mercedes-Benz',
                'description' => 'German luxury automotive brand.',
                'is_active' => true,
            ],
            [
                'name' => 'Audi',
                'description' => 'German luxury automobile manufacturer.',
                'is_active' => true,
            ],
            [
                'name' => 'Volkswagen',
                'description' => 'German automotive manufacturer.',
                'is_active' => true,
            ],
            [
                'name' => 'Lexus',
                'description' => 'Luxury vehicle division of Toyota.',
                'is_active' => true,
            ],
        ];

        foreach ($makes as $make) {
            Make::create($make);
        }
    }
}