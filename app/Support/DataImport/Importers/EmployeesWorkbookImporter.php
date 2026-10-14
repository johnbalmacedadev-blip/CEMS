<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;

class EmployeesWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'employees';
    }

    public function label(): string
    {
        return 'Employee List';
    }

    public function page(): array
    {
        return ['label' => 'Employees', 'route' => 'employees.index'];
    }

    public function tables(): array
    {
        return ['employees'];
    }

    protected function artisanCommand(): string
    {
        return 'import:employees';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);
        if (str_contains($name, 'EMPLOYEE')) {
            return true;
        }
        foreach ($sheetNames as $s) {
            if (stripos($s, 'EMPLOYEE DETAILS') !== false) {
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
            $header = $this->resolveEmployeeHeader($ws, $name);
            $supported = $header !== null;
            $rows = $header
                ? ExcelSheetHelper::countNonEmptyRows($ws, $header['row'], ExcelSheetHelper::col($header['map'], ['LAST NAME', 'FIRST NAME']))
                : 0;
            $out[] = [
                'name' => $name,
                'supported' => $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported ? null : 'Not an employee roster tab (skipped).',
            ];
        }
        $ss->disconnectWorksheets();

        return $out;
    }

    protected function parseRows(string $path, array $sheetNames): array
    {
        $ss = ExcelSheetHelper::load($path);
        $rows = [];
        foreach ($sheetNames as $name) {
            $ws = $ss->getSheetByName($name);
            if (! $ws) {
                continue;
            }
            $header = $this->resolveEmployeeHeader($ws, $name);
            if (! $header) {
                continue;
            }
            $map = $header['map'];
            $cFirst = ExcelSheetHelper::col($map, ['FIRST NAME']);
            $cMiddle = ExcelSheetHelper::col($map, ['MIDDLE NAME']);
            $cLast = ExcelSheetHelper::col($map, ['LAST NAME']);
            $cStart = ExcelSheetHelper::col($map, ['CONTRACT START']);
            $cType = ExcelSheetHelper::col($map, ['CONTRACT TYPE']);
            $cRole = ExcelSheetHelper::col($map, ['ROLE']);
            $cLoc = ExcelSheetHelper::col($map, ['LOC', 'LOCATION']);
            $cSss = ExcelSheetHelper::col($map, ['SSS']);
            $cPhil = ExcelSheetHelper::col($map, ['PHILHEALTH']);
            $cPag = ExcelSheetHelper::col($map, ['PAGIBIG']);
            $cBirth = ExcelSheetHelper::col($map, ['BIRTHDATE']);
            $cId = ExcelSheetHelper::col($map, ['ID NUMBER']);
            $cCur = ExcelSheetHelper::col($map, ['CURRENT ADDRESS']);
            $cPerm = ExcelSheetHelper::col($map, ['PERMANENT ADDRESS']);
            $cContact = ExcelSheetHelper::col($map, ['CONTACT NUMBERS', 'CONTACT']);
            $cEmail = ExcelSheetHelper::col($map, ['EMAIL']);

            $highest = (int) $ws->getHighestDataRow();
            $sectionStatus = 'active';
            for ($r = $header['row'] + 1; $r <= $highest; $r++) {
                $first = ExcelSheetHelper::cellString($ws, $cFirst, $r);
                $last = ExcelSheetHelper::cellString($ws, $cLast, $r);
                $contractType = ExcelSheetHelper::cellString($ws, $cType, $r);
                $role = ExcelSheetHelper::cellString($ws, $cRole, $r);
                $startRaw = ExcelSheetHelper::cellString($ws, $cStart, $r);

                // Section labels / repeated headers inside the roster.
                if ($first && ! $last && preg_match('/^terminated$/i', trim($first))) {
                    $sectionStatus = 'inactive';
                    continue;
                }
                if (strcasecmp((string) $first, 'FIRST NAME') === 0) {
                    continue;
                }

                // Stop once the REST DAY SCHEDULE block begins.
                if (stripos((string) $first, 'REST DAY') !== false
                    || stripos((string) $startRaw, 'REST DAY') !== false
                    || stripos((string) $role, 'REST DAY') !== false
                    || ($startRaw && preg_match('/^(A+|AA|L|SC|S)-?(SUN|MON|TUE|WED|THU|FRI|SAT)/i', $startRaw))) {
                    break;
                }

                if (! $first || ! $last) {
                    continue;
                }

                $notes = [];
                if ($id = ExcelSheetHelper::cellString($ws, $cId, $r)) {
                    $notes[] = 'ID: '.$id;
                }
                if ($contact = ExcelSheetHelper::cellString($ws, $cContact, $r)) {
                    $notes[] = 'Contact: '.$contact;
                }
                if ($email = ExcelSheetHelper::cellString($ws, $cEmail, $r)) {
                    $notes[] = 'Email: '.$email;
                }
                if ($cur = ExcelSheetHelper::cellString($ws, $cCur, $r)) {
                    $notes[] = 'Current address: '.$cur;
                }
                if ($perm = ExcelSheetHelper::cellString($ws, $cPerm, $r)) {
                    $notes[] = 'Permanent address: '.$perm;
                }

                $rows[] = [
                    'row' => $r,
                    'sheet' => $name,
                    'first_name' => $first,
                    'middle_name' => ExcelSheetHelper::cellString($ws, $cMiddle, $r),
                    'last_name' => $last,
                    'contract_start' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cStart, $r)),
                    'contract_type' => $contractType,
                    'role' => $role,
                    'location' => ExcelSheetHelper::cellString($ws, $cLoc, $r),
                    'sss' => ExcelSheetHelper::cellString($ws, $cSss, $r),
                    'philhealth' => ExcelSheetHelper::cellString($ws, $cPhil, $r),
                    'pagibig' => ExcelSheetHelper::cellString($ws, $cPag, $r),
                    'birthdate' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cBirth, $r)),
                    'status' => $sectionStatus,
                    'notes' => $notes ? implode(' | ', $notes) : null,
                ];
            }
        }
        $ss->disconnectWorksheets();

        return $rows;
    }

    /**
     * @return array{row:int,map:array<string,int>}|null
     */
    private function resolveEmployeeHeader($ws, string $sheetName): ?array
    {
        $u = strtoupper(trim($sheetName));
        // Roster tabs only — skip SAMPLE / START OF DEDUCTION payroll sheets.
        $isRoster = str_contains($u, 'EMPLOYEE DETAILS')
            || str_contains($u, 'SG ARAGON')
            || str_contains($u, 'DRIVER')
            || str_contains($u, 'CLEANER');
        if (! $isRoster || str_contains($u, 'DEDUCTION') || str_contains($u, 'SAMPLE')) {
            return null;
        }

        $header = ExcelSheetHelper::findHeaderMap($ws, ['FIRST NAME', 'LAST NAME']);
        if ($header
            && ExcelSheetHelper::col($header['map'], ['FIRST NAME']) !== null
            && ExcelSheetHelper::col($header['map'], ['LAST NAME']) !== null) {
            return $header;
        }

        // DRIVERSCLEANERS has no header labels; cols 2–8 are name/contract/role/loc.
        if (str_contains($u, 'DRIVER') || str_contains($u, 'CLEANER')) {
            $first = trim((string) ($ws->getCellByColumnAndRow(2, 2)->getValue() ?? ''));
            $last = trim((string) ($ws->getCellByColumnAndRow(4, 2)->getValue() ?? ''));
            if ($first !== '' && $last !== '') {
                return [
                    'row' => 1,
                    'map' => [
                        'FIRST NAME' => 2,
                        'MIDDLE NAME' => 3,
                        'LAST NAME' => 4,
                        'CONTRACT START' => 5,
                        'CONTRACT TYPE' => 6,
                        'ROLE' => 7,
                        'LOC' => 8,
                    ],
                ];
            }
        }

        return null;
    }
}
