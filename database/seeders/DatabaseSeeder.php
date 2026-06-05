<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@carempire.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create regular user
        User::create([
            'name' => 'John Developer',
            'email' => 'john@carempire.com',
            'password' => Hash::make('john123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Seed vehicles
        $this->call(VehicleSeeder::class);
        
        // Seed sales agents
        $this->call(SalesAgentSeeder::class);
        $this->call(ExecutiveAgentStaffSampleSeeder::class);

        // Seed employees
        $this->call(EmployeeSeeder::class);
    }
}
