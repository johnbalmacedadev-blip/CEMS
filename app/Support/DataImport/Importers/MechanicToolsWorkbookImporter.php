<?php

namespace App\Support\DataImport\Importers;

use App\Console\Commands\ImportMechanicToolsCommand;
use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MechanicToolsWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'mechanic_tools';
    }

    public function label(): string
    {
        return 'Mechanic Tools / Purchase Inventory';
    }

    public function page(): array
    {
        return ['label' => 'Mechanic Tools / Purchase Inventory', 'route' => 'mechanic-tools-expenses'];
    }

    public function tables(): array
    {
        return ['tools_inventory'];
    }

    protected function artisanCommand(): string
    {
        return 'import:mechanic-tools';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        // Command expects Excel path; we store a sidecar xlsx reference via JSON of rows and import directly.
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);
        if (str_contains($name, 'MECHANIC TOOLS') || (str_contains($name, 'TOOLS') && str_contains($name, 'EXPENSE'))) {
            return true;
        }
        foreach ($sheetNames as $s) {
            if (stripos($s, 'TOOLS INVENTORY') !== false) {
                return true;
            }
        }

        return false;
    }

    public function listSheets(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            $supported = strcasecmp($name, 'TOOLS INVENTORY') === 0;
            $rows = 0;
            if ($supported) {
                $cmd = new ImportMechanicToolsCommand;
                $rows = count($cmd->parseToolsInventory($path));
            }
            $out[] = [
                'name' => $name,
                'supported' => $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported ? 'Purchases (with amount) + dated inventory columns (Aug–Oct)' : 'Not imported into tools-purchase (PARTS / EXTERNAL EXPENSES use other sections).',
            ];
        }
        $ss->disconnectWorksheets();

        return $out;
    }

    protected function parseRows(string $path, array $sheetNames): array
    {
        if (! in_array('TOOLS INVENTORY', $sheetNames, true)) {
            // allow only selected supported sheet
            $wanted = false;
            foreach ($sheetNames as $s) {
                if (strcasecmp($s, 'TOOLS INVENTORY') === 0) {
                    $wanted = true;
                    break;
                }
            }
            if (! $wanted) {
                return [];
            }
        }

        $cmd = new ImportMechanicToolsCommand;
        $parsed = $cmd->parseToolsInventory($path);
        $out = [];
        foreach ($parsed as $i => $row) {
            $out[] = array_merge(['row' => $i + 1, 'sheet' => 'TOOLS INVENTORY'], $row);
        }

        return $out;
    }

    public function import(string $token, string $path, array $sheetNames): array
    {
        // Import directly from Excel path (command expects xlsx, not JSON)
        $tmpDir = storage_path('app/imports/'.$token.'_json');
        File::ensureDirectoryExists($tmpDir);

        $exit = Artisan::call('import:mechanic-tools', [
            '--file' => $path,
        ]);

        $rows = $this->parseRows($path, $sheetNames);

        return [
            'ok' => $exit === 0,
            'results' => [[
                'sheet' => implode(', ', $sheetNames),
                'command' => 'import:mechanic-tools',
                'branch' => '',
                'status' => $this->label(),
                'rows' => count($rows),
                'exit_code' => $exit,
                'output' => trim(Artisan::output()),
            ]],
        ];
    }
}
