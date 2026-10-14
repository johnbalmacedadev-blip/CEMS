<?php

namespace App\Console\Commands;

use App\Models\ExecutiveAgent;
use App\Models\Make;
use App\Models\SalesAgent;
use App\Models\SalesAgentCommission;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportSalesAgentCommissionsCommand extends Command
{
    protected $signature = 'import:sales-agent-commissions
                            {--file= : Path to JSON export}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import sales agents + commissions from 2026 SALES AGENT COMMISSIONS TRACKER Excel JSON';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/sales_agent_commissions_import.json');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $rows = json_decode(file_get_contents($path), true);
        if (! is_array($rows) || $rows === []) {
            $this->error('JSON file is empty or invalid.');

            return self::FAILURE;
        }

        $this->info('Commission rows: '.count($rows));
        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        $agentsCreated = 0;
        $executivesCreated = 0;
        $commissionsCreated = 0;
        $commissionsSkipped = 0;
        $vehiclesCreated = 0;
        $errors = 0;

        // Preload vehicles by plate
        $vehicleByPlate = [];
        foreach (Vehicle::query()->get(['id', 'plate_number']) as $vehicle) {
            $vehicleByPlate[$this->normalizePlate($vehicle->plate_number)] = $vehicle->id;
        }

        // Preload agents by normalized name
        $agentByName = [];
        foreach (SalesAgent::query()->get(['id', 'name']) as $agent) {
            $agentByName[$this->normalizeName($agent->name)] = $agent->id;
        }

        $execByName = [];
        foreach (ExecutiveAgent::query()->get(['id', 'name']) as $exec) {
            $execByName[$this->normalizeName($exec->name)] = $exec->id;
        }

        $makeCache = [];
        $modelCache = [];
        $agentCodeSeq = $this->nextAgentCodeNumber();
        $execCodeSeq = $this->nextExecCodeNumber();

        // Track which executive appears most for each agent (for linking)
        $agentExecVotes = [];

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            try {
                DB::transaction(function () use (
                    $row,
                    &$vehicleByPlate,
                    &$agentByName,
                    &$execByName,
                    &$makeCache,
                    &$modelCache,
                    &$agentCodeSeq,
                    &$execCodeSeq,
                    &$agentExecVotes,
                    &$agentsCreated,
                    &$executivesCreated,
                    &$commissionsCreated,
                    &$commissionsSkipped,
                    &$vehiclesCreated
                ) {
                    $plate = $this->normalizePlate($row['plate_number'] ?? '');
                    if ($plate === '') {
                        throw new \RuntimeException('Missing plate');
                    }

                    $agentNames = $row['agent_names'] ?? [];
                    if ($agentNames === []) {
                        throw new \RuntimeException('Missing agent name');
                    }

                    // Create/find all named agents; primary = first
                    $primaryAgentId = null;
                    foreach ($agentNames as $name) {
                        $norm = $this->normalizeName($name);
                        if (! isset($agentByName[$norm])) {
                            $agent = new SalesAgent([
                                'name' => $this->displayName($name),
                                'email' => $this->uniqueEmail($name),
                                'sales_agent_id' => 'SA'.str_pad((string) $agentCodeSeq++, 3, '0', STR_PAD_LEFT),
                                'department' => 'Sales',
                                'position' => str_contains(strtoupper((string) ($row['source'] ?? '')), 'REFERRAL')
                                    ? 'Referral Agent'
                                    : 'Sales Agent',
                                'commission_type' => SalesAgent::COMMISSION_CUSTOM,
                                'commission_rate' => 0,
                                'status' => 'active',
                                'notes' => 'Imported from 2026 Sales Agent Commissions Tracker',
                            ]);
                            $this->saveWithManualId($agent, ((int) SalesAgent::query()->max('id')) + 1);
                            $agentByName[$norm] = $agent->id;
                            $agentsCreated++;
                        }
                        if ($primaryAgentId === null) {
                            $primaryAgentId = $agentByName[$norm];
                        }
                    }

                    // Executives from SE names + sales person
                    $execCandidates = array_filter(array_merge(
                        $row['se_names'] ?? [],
                        [($row['sales_person'] ?? null)]
                    ));
                    foreach ($execCandidates as $execName) {
                        $norm = $this->normalizeName($execName);
                        if ($norm === '' || isset($execByName[$norm])) {
                            continue;
                        }
                        $exec = new ExecutiveAgent([
                            'name' => $this->displayName($execName),
                            'email' => $this->uniqueExecEmail($execName),
                            'executive_code' => 'EA'.str_pad((string) $execCodeSeq++, 3, '0', STR_PAD_LEFT),
                            'department' => 'Sales',
                            'status' => 'active',
                            'notes' => 'Imported from commissions tracker (SE / sales person)',
                        ]);
                        $this->saveWithManualId($exec, ((int) ExecutiveAgent::query()->max('id')) + 1);
                        $execByName[$norm] = $exec->id;
                        $executivesCreated++;
                    }

                    foreach ($execCandidates as $execName) {
                        $en = $this->normalizeName($execName);
                        if ($en === '' || ! isset($execByName[$en])) {
                            continue;
                        }
                        $agentExecVotes[$primaryAgentId][$execByName[$en]] =
                            ($agentExecVotes[$primaryAgentId][$execByName[$en]] ?? 0) + 1;
                    }

                    if (! isset($vehicleByPlate[$plate])) {
                        [$makeId, $makeName] = $this->resolveMake($row['make'] ?? 'Unknown', $makeCache);
                        [$modelId, $modelName] = $this->resolveModel($makeId, $row['model'] ?? 'Unknown', $modelCache);
                        $vehicle = new Vehicle([
                            'year' => (int) ($row['year'] ?? 2000),
                            'make_id' => $makeId,
                            'model_id' => $modelId,
                            'make' => $makeName,
                            'model' => $modelName,
                            'transmission' => 'Automatic',
                            'fuel_type' => 'Gasoline',
                            'kilometers' => 0,
                            'plate_number' => $plate,
                            'colour' => 'N/A',
                            'with_tools' => false,
                            'with_matting' => false,
                            'with_spare_tire' => false,
                            'purchase_price' => 0,
                            'posted_price' => 0,
                            'sold_price' => 0,
                            'purchased_from' => 'Commission Import',
                            'purchase_date' => $row['release_date'] ?? $row['reservation_date'] ?? now()->toDateString(),
                            'spare_key' => false,
                            'status' => 'Released',
                        ]);
                        $this->saveWithManualId($vehicle, ((int) Vehicle::query()->max('id')) + 1);
                        $vehicleByPlate[$plate] = $vehicle->id;
                        $vehiclesCreated++;
                    }

                    $vehicleId = $vehicleByPlate[$plate];
                    $amount = (float) ($row['amount'] ?? 0);
                    $releaseDate = $row['release_date'] ?? null;
                    $payDate = $row['date_of_payment'] ?? null;

                    // Dedupe on agent + plate/vehicle + amount (+ release/payment date when present).
                    $existsQuery = SalesAgentCommission::query()
                        ->where('sales_agent_id', $primaryAgentId)
                        ->where(function ($q) use ($plate, $vehicleId) {
                            $q->where('plate_number', $plate)->orWhere('vehicle_id', $vehicleId);
                        })
                        ->where('amount', $amount);
                    if ($releaseDate) {
                        $existsQuery->whereDate('release_date', $releaseDate);
                    } elseif ($payDate) {
                        $existsQuery->whereDate('date_of_payment', $payDate);
                    }
                    $exists = $existsQuery->exists();

                    if ($exists) {
                        $commissionsSkipped++;

                        return;
                    }

                    $ttype = strtoupper((string) ($row['transaction_type'] ?? 'CASH'));
                    if (! in_array($ttype, SalesAgentCommission::transactionTypeOptions(), true)) {
                        $ttype = str_contains($ttype, 'FINANC') ? 'FINANCING' : 'CASH';
                    }

                    $status = $row['commission_status'] ?? SalesAgentCommission::STATUS_PENDING;
                    if (! in_array($status, SalesAgentCommission::commissionStatusOptions(), true)) {
                        $status = SalesAgentCommission::STATUS_PENDING;
                    }

                    $commission = new SalesAgentCommission([
                        'showroom' => $row['showroom'] ?? null,
                        'commission_status' => $status,
                        'sales_agent_id' => $primaryAgentId,
                        'agent_name' => $this->displayName($agentNames[0]),
                        'client_name' => null,
                        'unit' => $row['unit'] ?? null,
                        'vehicle_id' => $vehicleId,
                        'plate_number' => $plate,
                        'transaction_type' => $ttype,
                        'release_date' => $releaseDate,
                        'amount' => $amount,
                        'agents_folder_amount' => $row['agents_folder_amount'] ?? null,
                        'sales_executive_commission' => $row['sales_executive_commission'] ?? null,
                        'proof_of_appointment' => $row['proof_of_appointment'] ?? false,
                        'sign_client_with_agent' => $row['sign_client_with_agent'] ?? false,
                        'date_sent' => $row['date_sent'] ?? $payDate,
                        'date_of_payment' => $payDate,
                        'notes' => $row['notes'] ?? null,
                    ]);
                    $this->saveWithManualId($commission, ((int) SalesAgentCommission::query()->max('id')) + 1);
                    $commissionsCreated++;
                });
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error(
                    ($row['sheet'] ?? '?').
                    ' row '.($row['row'] ?? '?').
                    ' plate '.($row['plate_number'] ?? '?').
                    ': '.$e->getMessage()
                );
            }

            $bar->advance();
        }

        // Link each sales agent to most-voted executive
        foreach ($agentExecVotes as $agentId => $votes) {
            arsort($votes);
            $execId = array_key_first($votes);
            if ($execId) {
                SalesAgent::where('id', $agentId)->whereNull('executive_agent_id')->update([
                    'executive_agent_id' => $execId,
                ]);
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Sales agents created', $agentsCreated],
                ['Executive agents created', $executivesCreated],
                ['Commissions created', $commissionsCreated],
                ['Commissions skipped (dup)', $commissionsSkipped],
                ['Vehicles created', $vehiclesCreated],
                ['Errors', $errors],
                ['Total agents now', SalesAgent::count()],
                ['Total commissions now', SalesAgentCommission::count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $plate) ?? '');
    }

    private function normalizeName(?string $name): string
    {
        $n = strtoupper(trim((string) $name));
        $n = preg_replace('/\s+/', ' ', $n) ?? '';
        // fold common encoding issues
        $n = str_replace(['Ñ', 'ñ'], 'N', $n);

        return $n;
    }

    private function displayName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');

        return mb_convert_case(mb_strtolower($name), MB_CASE_TITLE, 'UTF-8');
    }

    private function uniqueEmail(string $name): string
    {
        $slug = Str::slug(Str::limit($name, 48, ''), '.');
        if ($slug === '') {
            $slug = 'agent';
        }
        do {
            $email = $slug.'.'.Str::lower(Str::random(8)).'@noreply.carempire.local';
        } while (SalesAgent::where('email', $email)->exists());

        return $email;
    }

    private function uniqueExecEmail(string $name): string
    {
        $slug = Str::slug(Str::limit($name, 48, ''), '.');
        if ($slug === '') {
            $slug = 'exec';
        }
        do {
            $email = $slug.'.'.Str::lower(Str::random(8)).'@noreply.carempire.local';
        } while (ExecutiveAgent::where('email', $email)->exists());

        return $email;
    }

    private function nextAgentCodeNumber(): int
    {
        $max = 0;
        foreach (SalesAgent::query()->pluck('sales_agent_id') as $code) {
            if (preg_match('/^SA(\d+)$/i', (string) $code, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $max + 1;
    }

    private function nextExecCodeNumber(): int
    {
        $max = 0;
        foreach (ExecutiveAgent::query()->pluck('executive_code') as $code) {
            if (preg_match('/^EA(\d+)$/i', (string) $code, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $max + 1;
    }

    /**
     * @param  array<string, array{0:int,1:string}>  $cache
     * @return array{0:int,1:string}
     */
    private function resolveMake(string $name, array &$cache): array
    {
        $key = strtoupper(trim($name));
        if ($key === '') {
            $key = 'UNKNOWN';
            $name = 'Unknown';
        }
        if (isset($cache[$key])) {
            return $cache[$key];
        }
        $make = Make::query()->whereRaw('UPPER(name) = ?', [$key])->first();
        if (! $make) {
            $make = new Make(['name' => ucwords(strtolower($name)), 'is_active' => true]);
            $this->saveWithManualId($make, ((int) Make::query()->max('id')) + 1);
        }
        $cache[$key] = [$make->id, $make->name];

        return $cache[$key];
    }

    /**
     * @param  array<string, array{0:int,1:string}>  $cache
     * @return array{0:int,1:string}
     */
    private function resolveModel(int $makeId, string $name, array &$cache): array
    {
        $trimmed = trim($name) ?: 'Unknown';
        $key = $makeId.'|'.strtoupper($trimmed);
        if (isset($cache[$key])) {
            return $cache[$key];
        }
        $model = VehicleModel::query()
            ->where('make_id', $makeId)
            ->whereRaw('UPPER(name) = ?', [strtoupper($trimmed)])
            ->first();
        if (! $model) {
            $model = new VehicleModel([
                'make_id' => $makeId,
                'name' => ucwords(strtolower($trimmed)),
                'is_active' => true,
            ]);
            $this->saveWithManualId($model, ((int) VehicleModel::query()->max('id')) + 1);
        }
        $cache[$key] = [$model->id, $model->name];

        return $cache[$key];
    }

    /**
     * Persist with an explicit id on DBs that lack AUTO_INCREMENT.
     */
    private function saveWithManualId(\Illuminate\Database\Eloquent\Model $model, int $id): void
    {
        $model->id = $id;
        $model->incrementing = false;
        $model->save();
    }
}
