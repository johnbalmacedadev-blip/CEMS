<?php

namespace App\Support\DataImport;

use App\Support\DataImport\Importers\MechanicToolsWorkbookImporter;
use App\Support\DataImport\Importers\BuffingWorkbookImporter;
use App\Support\DataImport\Importers\EmployeesWorkbookImporter;
use App\Support\DataImport\Importers\GasPoWorkbookImporter;
use App\Support\DataImport\Importers\InsuranceWorkbookImporter;
use App\Support\DataImport\Importers\MechanicWorkbookImporter;
use App\Support\DataImport\Importers\RecommendationWorkbookImporter;
use App\Support\DataImport\Importers\SalesAgentCommissionsWorkbookImporter;
use App\Support\DataImport\Importers\SoaExpensesWorkbookImporter;
use App\Support\DataImport\Importers\TransferOrcrWorkbookImporter;
use App\Support\DataImport\Importers\UnitsMasterlistWorkbookImporter;
use App\Support\DataImport\Importers\UnitsWorkbookImporter;
use App\Support\DataImport\Importers\VideoPostingWorkbookImporter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DataImportManager
{
    /** @return array<int, WorkbookImporterInterface> */
    public static function importers(): array
    {
        return [
            new UnitsWorkbookImporter,
            new MechanicToolsWorkbookImporter,
            new EmployeesWorkbookImporter,
            new InsuranceWorkbookImporter,
            new BuffingWorkbookImporter,
            new MechanicWorkbookImporter,
            new VideoPostingWorkbookImporter,
            new RecommendationWorkbookImporter,
            new GasPoWorkbookImporter,
            new SalesAgentCommissionsWorkbookImporter,
            new SoaExpensesWorkbookImporter,
            new TransferOrcrWorkbookImporter,
            new UnitsMasterlistWorkbookImporter,
        ];
    }

    public static function supportedCatalog(): array
    {
        return array_map(function (WorkbookImporterInterface $imp) {
            $page = $imp->page();

            return [
                'key' => $imp->key(),
                'label' => $imp->label(),
                'tables' => $imp->tables(),
                'page' => $page['label'],
                'route' => $page['route'],
            ];
        }, self::importers());
    }

    public static function storeUpload($uploadedFile): array
    {
        $dir = storage_path('app/imports');
        File::ensureDirectoryExists($dir);

        $token = (string) Str::uuid();
        $filename = $token.'.xlsx';
        $uploadedFile->move($dir, $filename);
        $path = $dir.DIRECTORY_SEPARATOR.$filename;
        $original = method_exists($uploadedFile, 'getClientOriginalName')
            ? $uploadedFile->getClientOriginalName()
            : $filename;

        $sheetNames = ExcelSheetHelper::sheetNames($path);
        $matches = [];
        foreach (self::importers() as $importer) {
            if ($importer->matches($original, $sheetNames)) {
                $matches[] = $importer;
            }
        }

        if ($matches === []) {
            return [
                'token' => $token,
                'original_name' => $original,
                'workbook_type' => null,
                'workbook_label' => null,
                'sheets' => array_map(fn ($n) => [
                    'name' => $n,
                    'supported' => false,
                    'excel_rows' => 0,
                    'tables' => [],
                    'note' => 'Workbook type not recognized. Supported types are listed on this page.',
                ], $sheetNames),
                'message' => 'Could not detect workbook type from filename or sheet tabs.',
            ];
        }

        // Prefer strongest filename match order already in importers list
        $importer = $matches[0];
        $sheets = $importer->listSheets($path);
        $page = $importer->page();

        return [
            'token' => $token,
            'original_name' => $original,
            'workbook_type' => $importer->key(),
            'workbook_label' => $importer->label(),
            'page' => $page,
            'tables' => $importer->tables(),
            'sheets' => $sheets,
            'candidates' => array_map(fn ($i) => ['key' => $i->key(), 'label' => $i->label()], $matches),
        ];
    }

    public static function pathForToken(string $token): string
    {
        $path = storage_path('app/imports/'.$token.'.xlsx');
        if (! is_file($path)) {
            throw new \RuntimeException('Import file expired or not found. Please upload again.');
        }

        return $path;
    }

    public static function resolveImporter(string $type): WorkbookImporterInterface
    {
        foreach (self::importers() as $importer) {
            if ($importer->key() === $type) {
                return $importer;
            }
        }
        throw new \InvalidArgumentException('Unknown workbook type: '.$type);
    }

    public static function analyze(string $token, string $type, array $sheets): array
    {
        $path = self::pathForToken($token);
        $importer = self::resolveImporter($type);

        return $importer->analyze($path, $sheets);
    }

    public static function import(string $token, string $type, array $sheets): array
    {
        $path = self::pathForToken($token);
        $importer = self::resolveImporter($type);

        return $importer->import($token, $path, $sheets);
    }
}
