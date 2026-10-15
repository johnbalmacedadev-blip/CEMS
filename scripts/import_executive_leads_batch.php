<?php

/**
 * One-off batch import of executive agent leads masterlists.
 * Usage: php scripts/import_executive_leads_batch.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExecutiveAgent;
use App\Models\ExecutiveAgentLead;
use App\Support\ExecutiveAgentLeadsWorkbookParser;
use Illuminate\Support\Facades\DB;

$downloads = 'C:\\Users\\VJ Kyle\\Downloads';

$jobs = [
    ['file' => $downloads.'\\LYN SALES TEAM LEADS MASTERLIST.xlsx', 'agent' => 'Lyn'],
    ['file' => $downloads.'\\JIMMY SALES TEAM LEADS MASTERLIST (1).xlsx', 'agent' => 'Jimmy'],
    ['file' => $downloads.'\\RON SALES TEAM LEADS MASTERLIST.xlsx', 'agent' => 'Ron'],
    ['file' => $downloads.'\\GOTCHI SALES TEAM LEADS MASTERLIST.xlsx', 'agent' => 'Gotchi'],
    ['file' => $downloads.'\\ALY SALES TEAM LEADS MASTERLIST.xlsx', 'agent' => 'Alyssa'],
    ['file' => $downloads.'\\GEOFFREY SALES TEAM LEADS MASTERLIST.xlsx', 'agent' => 'Geoff'],
    ['file' => $downloads.'\\THYRA SALES TEAM LEADS MASTERLIST.xlsx', 'agent' => 'Thyra'],
    ['file' => $downloads.'\\JESON SALES TEAM LEADS MASTERLIST.xlsx', 'agent' => 'Jeson'],
];

$leadKey = static function (?string $date, ?string $name, ?string $phone): string {
    $normPhone = strtoupper(preg_replace('/\D+/', '', (string) $phone) ?? '');
    $normName = strtoupper(preg_replace('/\s+/', ' ', trim((string) $name)) ?? '');

    return ($date ?: '').'|'.$normName.'|'.$normPhone;
};

$parser = new ExecutiveAgentLeadsWorkbookParser();

foreach ($jobs as $job) {
    $path = $job['file'];
    $agentName = $job['agent'];

    if (! is_file($path)) {
        echo "MISSING FILE: {$path}\n";
        continue;
    }

    $executive = ExecutiveAgent::query()
        ->whereRaw('LOWER(name) = ?', [strtolower($agentName)])
        ->first();

    if (! $executive) {
        echo "AGENT NOT FOUND: {$agentName}\n";
        continue;
    }

    echo "=== {$executive->name} (id {$executive->id}) ← ".basename($path)." ===\n";

    try {
        $rows = $parser->parse($path);
    } catch (Throwable $e) {
        echo "PARSE ERROR: {$e->getMessage()}\n";
        continue;
    }

    echo 'Parsed rows: '.count($rows)."\n";

    $existingKeys = [];
    foreach (
        ExecutiveAgentLead::query()
            ->where('executive_agent_id', $executive->id)
            ->get(['application_date', 'customer_name', 'customer_phone']) as $lead
    ) {
        $existingKeys[$leadKey(
            optional($lead->application_date)->format('Y-m-d'),
            $lead->customer_name,
            $lead->customer_phone
        )] = true;
    }

    $created = 0;
    $skipped = 0;
    $errors = 0;

    foreach ($rows as $row) {
        try {
            $key = $leadKey(
                $row['application_date'] ?? null,
                $row['customer_name'] ?? '',
                $row['customer_phone'] ?? ''
            );
            if (isset($existingKeys[$key])) {
                $skipped++;
                continue;
            }

            DB::transaction(function () use ($executive, $row, &$created, &$existingKeys, $key) {
                $lead = new ExecutiveAgentLead(array_merge($row, [
                    'executive_agent_id' => $executive->id,
                ]));
                $lead->id = ((int) ExecutiveAgentLead::query()->max('id')) + 1;
                $lead->incrementing = false;
                $lead->save();
                $existingKeys[$key] = true;
                $created++;
            });
        } catch (Throwable $e) {
            $errors++;
            if ($errors <= 3) {
                echo "  row error: {$e->getMessage()}\n";
            }
        }
    }

    echo "Result: {$created} created, {$skipped} skipped, {$errors} errors\n\n";
}

echo "Done.\n";
