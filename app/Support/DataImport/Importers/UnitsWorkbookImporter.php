<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\WorkbookImporterInterface;
use App\Support\VehicleUnitsExcelImporter;

class UnitsWorkbookImporter implements WorkbookImporterInterface
{
    public function key(): string
    {
        return 'units';
    }

    public function label(): string
    {
        return 'Available / Reserved / Released Units';
    }

    public function page(): array
    {
        return ['label' => 'Unit Report', 'route' => 'vehicles.index'];
    }

    public function tables(): array
    {
        return ['vehicles', 'vehicle_expenses', 'vehicle_status_details', 'vehicle_forfeit_details'];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);
        if (str_contains($name, 'AVAILABLE') && str_contains($name, 'RESERVED')) {
            return true;
        }
        foreach ($sheetNames as $sheet) {
            if (VehicleUnitsExcelImporter::resolveTabMeta($sheet) !== null) {
                return true;
            }
        }

        return false;
    }

    public function listSheets(string $path): array
    {
        return VehicleUnitsExcelImporter::listSheets($path);
    }

    public function analyze(string $path, array $sheetNames): array
    {
        $token = basename($path, '.xlsx');
        $summary = VehicleUnitsExcelImporter::analyze($token, $sheetNames);
        $summary['workbook_type'] = $this->key();
        $summary['workbook_label'] = $this->label();
        $summary['page'] = $this->page();

        return $summary;
    }

    public function import(string $token, string $path, array $sheetNames): array
    {
        return VehicleUnitsExcelImporter::import($token, $sheetNames);
    }
}
