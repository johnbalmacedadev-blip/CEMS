<?php

namespace App\Support\DataImport;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

abstract class AbstractJsonWorkbookImporter implements WorkbookImporterInterface
{
    abstract protected function artisanCommand(): string;

    /** @return array<string, mixed> */
    abstract protected function artisanParams(string $jsonPath, array $sheetNames): array;

    abstract protected function parseRows(string $path, array $sheetNames): array;

    public function analyze(string $path, array $sheetNames): array
    {
        $rows = $this->parseRows($path, $sheetNames);
        $bySheet = [];
        foreach ($rows as $row) {
            $sheet = (string) ($row['sheet'] ?? $row['source_sheet'] ?? 'Sheet');
            $bySheet[$sheet] = ($bySheet[$sheet] ?? 0) + 1;
        }

        $tabs = [];
        foreach ($sheetNames as $name) {
            $tabs[] = [
                'name' => $name,
                'status' => $this->label(),
                'branch' => '',
                'excel_rows' => $bySheet[$name] ?? 0,
                'unique_plates' => null,
                'will_create_vehicles' => null,
                'will_update_vehicles' => null,
                'tables' => $this->tables(),
                'command' => $this->artisanCommand(),
            ];
        }

        $tableSummary = [];
        foreach ($this->tables() as $table) {
            $tableSummary[] = [
                'table' => $table,
                'action' => 'insert / update (merge)',
                'approx_rows_touched' => count($rows),
                'description' => 'Imported from '.$this->label(),
            ];
        }

        $page = $this->page();

        return [
            'workbook_type' => $this->key(),
            'workbook_label' => $this->label(),
            'page' => $page,
            'tabs' => $tabs,
            'tables' => $tableSummary,
            'totals' => [
                'excel_rows' => count($rows),
                'unique_plates' => 0,
                'will_create_vehicles' => 0,
                'will_update_vehicles' => 0,
                'will_skip' => 0,
                'rows_to_process' => count($rows),
            ],
            'notes' => [
                'Destination page: '.$page['label'].($page['route'] ? ' ('.route($page['route']).')' : ''),
                'Selected tabs will be parsed and merged into the listed tables (no truncate).',
                count($rows).' data row(s) will be sent to '.$this->artisanCommand().'.',
            ],
        ];
    }

    public function import(string $token, string $path, array $sheetNames): array
    {
        $rows = $this->parseRows($path, $sheetNames);
        if ($rows === []) {
            throw new \RuntimeException('No importable rows found in the selected tabs.');
        }

        $tmpDir = storage_path('app/imports/'.$token.'_json');
        File::ensureDirectoryExists($tmpDir);
        $jsonPath = $tmpDir.DIRECTORY_SEPARATOR.$this->key().'.json';
        File::put($jsonPath, json_encode($rows, JSON_UNESCAPED_UNICODE));

        $exit = Artisan::call($this->artisanCommand(), $this->artisanParams($jsonPath, $sheetNames));

        return [
            'ok' => $exit === 0,
            'results' => [[
                'sheet' => implode(', ', $sheetNames),
                'command' => $this->artisanCommand(),
                'branch' => '',
                'status' => $this->label(),
                'rows' => count($rows),
                'exit_code' => $exit,
                'output' => trim(Artisan::output()),
            ]],
        ];
    }
}
