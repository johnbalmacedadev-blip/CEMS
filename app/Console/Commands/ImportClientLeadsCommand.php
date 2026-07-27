<?php

namespace App\Console\Commands;

use App\Models\ClientFollowUp;
use App\Models\ExecutiveAgent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportClientLeadsCommand extends Command
{
    protected $signature = 'import:client-leads
                            {--file= : Path to JSON export}
                            {--fresh : Truncate client_follow_up_list before import}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import CLIENT LISTS sales team lead masterlists into Client List';

    /** @var array<string,string> */
    private const TEAM_ALIASES = [
        'ALYSSA' => 'Alyssa',
        'ALY' => 'Alyssa',
        'GEOFFREY' => 'Geoff',
        'GEOFF' => 'Geoff',
        'GOTCHI' => 'Gotchi',
        'JESON' => 'Jeson',
        'JIMMY' => 'Jimmy',
        'JM' => 'Jm',
        'LYN' => 'Lyn',
        'RON' => 'Ron',
        'THYRA' => 'Thyra',
    ];

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/client_leads_import.json');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $rows = json_decode(file_get_contents($path), true);
        if (! is_array($rows) || $rows === []) {
            $this->error('JSON file is empty or invalid.');

            return self::FAILURE;
        }

        $this->info('Rows to import: '.count($rows));
        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            ClientFollowUp::query()->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->warn('client_follow_up_list truncated.');
        }

        $execMap = $this->ensureExecutives();

        $created = 0;
        $errors = 0;
        $byTeam = [];
        $now = now();
        $chunkSize = 150;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach (array_chunk($rows, $chunkSize) as $chunk) {
            $payload = [];
            foreach ($chunk as $row) {
                try {
                    $name = trim((string) ($row['client_name'] ?? ''));
                    if ($name === '') {
                        throw new \RuntimeException('Missing client_name');
                    }

                    $team = strtoupper(trim((string) ($row['team_lead'] ?? '')));
                    $execId = $execMap[$team] ?? null;
                    $byTeam[$team] = ($byTeam[$team] ?? 0) + 1;

                    $payload[] = [
                        'executive_agent_id' => $execId,
                        'team_lead' => $team !== '' ? $team : null,
                        'date_of_first_inquiry' => $row['date_of_first_inquiry'] ?? null,
                        'application' => $this->clip($row['application'] ?? null, 255),
                        'client_name' => $this->clip($name, 255),
                        'contact_number' => $this->clip($row['contact_number'] ?? null, 100),
                        'email' => null,
                        'vehicle_id' => null,
                        'unit_inquired' => $this->clip($row['unit_inquired'] ?? null, 255),
                        'notes' => $row['notes'] ?? null,
                        'about_what' => $this->clip($row['about_what'] ?? null, 500),
                        'sales_exec_1' => $this->clip($row['sales_exec_1'] ?? null, 100),
                        'date_followed_up_1' => $row['date_followed_up_1'] ?? null,
                        'outcome_1' => $this->clip($row['outcome_1'] ?? null, 255),
                        'notes_1' => $row['notes_1'] ?? null,
                        'sales_exec_2' => $this->clip($row['sales_exec_2'] ?? null, 100),
                        'date_followed_up_2' => $row['date_followed_up_2'] ?? null,
                        'outcome_2' => $this->clip($row['outcome_2'] ?? null, 255),
                        'notes_2' => $row['notes_2'] ?? null,
                        'sales_exec_3' => $this->clip($row['sales_exec_3'] ?? null, 100),
                        'date_followed_up_3' => $row['date_followed_up_3'] ?? null,
                        'outcome_3' => $this->clip($row['outcome_3'] ?? null, 255),
                        'notes_3' => $row['notes_3'] ?? null,
                        'sales_exec_4' => $this->clip($row['sales_exec_4'] ?? null, 100),
                        'date_followed_up_4' => $row['date_followed_up_4'] ?? null,
                        'outcome_4' => $this->clip($row['outcome_4'] ?? null, 255),
                        'notes_4' => $row['notes_4'] ?? null,
                        'sales_exec_5' => $this->clip($row['sales_exec_5'] ?? null, 100),
                        'date_followed_up_5' => $row['date_followed_up_5'] ?? null,
                        'outcome_5' => $this->clip($row['outcome_5'] ?? null, 255),
                        'notes_5' => $row['notes_5'] ?? null,
                        'follow_up_date' => $row['follow_up_date'] ?? null,
                        'status' => $row['status'] ?? 'Pending',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                } catch (\Throwable $e) {
                    $errors++;
                    $this->newLine();
                    $this->error('Row '.($row['row'] ?? '?').' ('.($row['team_lead'] ?? '?').'): '.$e->getMessage());
                }
                $bar->advance();
            }

            if ($payload !== []) {
                ClientFollowUp::insert($payload);
                $created += count($payload);
            }
        }

        $bar->finish();
        $this->newLine(2);

        $teamRows = [];
        foreach ($byTeam as $team => $count) {
            $teamRows[] = [$team, $count];
        }
        $this->table(['Team', 'Imported'], $teamRows);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created', $created],
                ['Errors', $errors],
                ['Total clients', ClientFollowUp::count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array<string,int> team_lead uppercase => executive_agent_id
     */
    private function ensureExecutives(): array
    {
        $map = [];
        $existing = ExecutiveAgent::query()->get(['id', 'name', 'executive_code']);

        foreach (self::TEAM_ALIASES as $teamKey => $canonicalName) {
            $found = $existing->first(function ($e) use ($canonicalName) {
                return strcasecmp((string) $e->name, $canonicalName) === 0;
            });

            if (! $found) {
                $codeBase = 'EA'.strtoupper(Str::substr(preg_replace('/\s+/', '', $canonicalName), 0, 6));
                $code = $codeBase;
                $i = 1;
                while (ExecutiveAgent::where('executive_code', $code)->exists()) {
                    $code = $codeBase.$i;
                    $i++;
                }

                $found = ExecutiveAgent::create([
                    'name' => $canonicalName,
                    'executive_code' => $code,
                    'status' => 'active',
                    'notes' => 'Auto-created from CLIENT LISTS import',
                ]);
                $this->line("Created executive agent: {$canonicalName} ({$code})");
                $existing->push($found);
            }

            $map[$teamKey] = (int) $found->id;
        }

        foreach ($map as $team => $id) {
            $this->line("  {$team} → executive #{$id}");
        }

        return $map;
    }

    private function clip(?string $value, int $max): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim($value);

        return $value === '' ? null : mb_substr($value, 0, $max);
    }
}
