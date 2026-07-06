<?php

namespace Database\Seeders;

use App\Models\TrailFormClient;
use Illuminate\Database\Seeder;

class TrailFormClientSeeder extends Seeder
{
    public function run(): void
    {
        if (TrailFormClient::count() > 0) {
            return;
        }

        $samples = [
            ['2026-06-01', 'Maria Santos', '09171234567', 'maria.s@email.com', 'Inquiring', 'Facebook', 'SUV', 'Toyota Fortuner 2020', 'Asked about financing options.'],
            ['2026-06-02', 'Juan Dela Cruz', '09189876543', null, 'Reservation', 'Walk-in', 'Sedan', 'Honda Civic 2019', 'Reserved for viewing Saturday.'],
            ['2026-06-03', 'Ana Reyes', '09201112233', 'ana.reyes@email.com', 'Inquiring', 'Instagram', 'Hatchback', 'Mazda 2', 'Interested in low down payment.'],
            ['2026-06-04', 'Mark Villanueva', '09334445566', null, 'Inquiring', 'TikTok', 'Pickup', 'Ford Ranger', 'Wants diesel unit.'],
            ['2026-06-05', 'Grace Lim', '09456667788', 'grace.lim@email.com', 'Reservation', 'Referral', 'MPV', 'Toyota Innova 2021', 'Referred by existing client.'],
            ['2026-06-06', 'Robert Tan', '09567778899', null, 'Inquiring', 'Phone Call', 'Sedan', 'Toyota Vios', 'Calling back next week.'],
            ['2026-06-07', 'Lisa Fernandez', '09678889900', 'lisa.f@email.com', 'Inquiring', 'Website', 'Crossover', 'Hyundai Tucson', 'Requested brochure via email.'],
            ['2026-06-08', 'Carlo Mendoza', '09789990011', null, 'Reservation', 'Facebook', 'SUV', 'Mitsubishi Montero Sport', 'Deposit discussion pending.'],
            ['2026-06-09', 'Patricia Gomez', '09890001122', 'pat.gomez@email.com', 'Inquiring', 'Email', 'Van', 'Nissan Urvan', 'Fleet inquiry for 2 units.'],
            ['2026-06-10', 'James Ong', '09901112233', null, 'Inquiring', 'Walk-in', 'Coupe', 'BMW 3 Series', 'Cash buyer, prefers white color.'],
        ];

        foreach ($samples as $row) {
            TrailFormClient::create([
                'inquiry_date' => $row[0],
                'client_name' => $row[1],
                'contact_number' => $row[2],
                'email' => $row[3],
                'status' => $row[4],
                'inquiry_source' => $row[5],
                'vehicle_type' => $row[6],
                'vehicle_interest' => $row[7],
                'notes' => $row[8],
            ]);
        }
    }
}
