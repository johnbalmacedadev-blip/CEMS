<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tool;
use Illuminate\Support\Facades\DB;

class StandardizeToolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapping of old names to standardized names
        $nameMapping = [
            '4PCS PLIER SET' => '1 SET PLIERS',
            '18PCS SCREW' => '1 SET SCREW DRIVER',
            '152PCS SET CIWLI2050-24353910191 (IMPACT WRENCH)' => 'CORDLESS IMPACT WRENCH',
            'FLOOR JAC' => 'HYDRAULIC FLOOR STAND',
            'DUAL MANIFOLD GAUGE' => 'MANIFOLD GAUGE',
            '12PCS OFF SET TOOLS' => 'OFF SET RING',
            'VERNIER CALIPER' => 'CALIPER',
            'ELECTRIC SOLDERING IRON - TOOL MWL CORP' => 'SOLDERING IRON',
            'EXTENSION WIRE - TOOL MWL CORP' => 'EXTENSION WIRE',
            'POWERCRAFT GRINDER 4 PAG 8-105SE (ALYSSA - ANNEX)' => 'GRINDER',
        ];

        // Update existing tools with standardized names
        foreach ($nameMapping as $oldName => $newName) {
            Tool::where('name', $oldName)->update(['name' => $newName]);
        }

        // List of all standardized tool names that should exist
        $standardizedTools = [
            '1 SET PLIERS',
            '1 SET SCREW DRIVER',
            'ADJUSTABLE WRENCH',
            'BALLPEEN HAMMER',
            'CALIPER',
            'CORDLESS IMPACT WRENCH',
            'CURVED JAW LOCKING PLIER',
            'EXTENSION WIRE',
            'GRINDER',
            'HYDRAULIC FLOOR STAND',
            'JACK STAND',
            'MANIFOLD GAUGE',
            'OFF SET RING',
            'RUBBER AND PLASTIC HAMMER',
            'SCANNER (ANNEX)',
            'SOLDERING IRON',
            'VACUUM PUMP',
            'YABE TUBO',
        ];

        // Ensure all standardized tools exist (add missing ones)
        $existingTools = Tool::distinct()->pluck('name')->toArray();
        
        foreach ($standardizedTools as $toolName) {
            if (!in_array($toolName, $existingTools)) {
                // Add a placeholder entry with today's date if tool doesn't exist
                Tool::create([
                    'name' => $toolName,
                    'quantity' => 0,
                    'amount' => 0.00,
                    'date_acquired' => now()->toDateString(),
                ]);
            }
        }

        $this->command->info('Tool names have been standardized successfully!');
    }
}
