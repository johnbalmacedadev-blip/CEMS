<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesAgent;

class UpdateSalesAgentIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales-agents:update-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update sales agent IDs to use SA format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $agents = SalesAgent::all();
        
        $this->info('Updating sales agent IDs...');
        
        foreach ($agents as $agent) {
            $oldId = $agent->sales_agent_id;
            $agent->sales_agent_id = 'SA' . str_pad($agent->id, 3, '0', STR_PAD_LEFT);
            $agent->save();
            
            $this->line("Updated {$agent->name}: {$oldId} -> {$agent->sales_agent_id}");
        }
        
        $this->info('All sales agent IDs have been updated!');
    }
}