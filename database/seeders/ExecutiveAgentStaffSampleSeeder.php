<?php

namespace Database\Seeders;

use App\Models\ExecutiveAgent;
use App\Models\SalesAgent;
use App\Models\SalesAgentCommission;
use Illuminate\Database\Seeder;

class ExecutiveAgentStaffSampleSeeder extends Seeder
{
    /**
     * Executive agents, link sales agents, and sample commission rows for staff reports.
     */
    public function run(): void
    {
        $execNorth = ExecutiveAgent::updateOrCreate(
            ['executive_code' => 'EA-NORTH'],
            [
                'name' => 'Evelyn Mercado',
                'email' => 'evelyn.mercado@carempire.com',
                'phone' => '+63 917 100 0001',
                'department' => 'Sales',
                'status' => 'active',
                'notes' => 'Executive — North team',
            ]
        );

        $execSouth = ExecutiveAgent::updateOrCreate(
            ['executive_code' => 'EA-SOUTH'],
            [
                'name' => 'Chess Ramirez',
                'email' => 'chess.ramirez@carempire.com',
                'phone' => '+63 917 100 0002',
                'department' => 'Sales',
                'status' => 'active',
                'notes' => 'Executive — South team',
            ]
        );

        $northCodes = ['SA001', 'SA002', 'SA003', 'SA004', 'SA005', 'SA006', 'SA007', 'SA008', 'SA009', 'SA010'];
        $southCodes = ['SA011', 'SA012', 'SA013', 'SA014', 'SA015', 'SA016', 'SA017', 'SA018', 'SA019', 'SA020'];

        SalesAgent::whereIn('sales_agent_id', $northCodes)->update(['executive_agent_id' => $execNorth->id]);
        SalesAgent::whereIn('sales_agent_id', $southCodes)->update(['executive_agent_id' => $execSouth->id]);

        $samples = [
            ['SA001', 18500.50],
            ['SA001', 12200.00],
            ['SA002', 9800.75],
            ['SA003', 15000.00],
            ['SA011', 7600.25],
            ['SA012', 21000.00],
            ['SA015', 4500.00],
        ];

        foreach ($samples as [$code, $amount]) {
            $agent = SalesAgent::where('sales_agent_id', $code)->first();
            if (!$agent) {
                continue;
            }
            SalesAgentCommission::create([
                'showroom' => 'FLAGSHIP',
                'sales_agent_id' => $agent->id,
                'agent_name' => $agent->name,
                'client_name' => 'Sample Client',
                'unit' => 'Sample unit',
                'vehicle_id' => null,
                'plate_number' => null,
                'transaction_type' => SalesAgentCommission::TRANSACTION_CASH,
                'release_date' => now()->subDays(rand(5, 60)),
                'amount' => $amount,
                'agents_folder_amount' => null,
                'sales_executive_commission' => null,
                'proof_of_appointment' => false,
                'sign_client_with_agent' => false,
                'date_sent' => now()->subDays(rand(1, 30)),
                'date_of_payment' => null,
                'notes' => 'Sample commission (ExecutiveAgentStaffSampleSeeder)',
            ]);
        }
    }
}
