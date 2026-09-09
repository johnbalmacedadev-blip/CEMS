<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;

class SalesAgentCommissionsWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'sales_commissions';
    }

    public function label(): string
    {
        return 'Sales Agent Commissions';
    }

    public function page(): array
    {
        return ['label' => 'Sales Agent Commissions', 'route' => 'sales-agent-commissions.index'];
    }

    public function tables(): array
    {
        return ['sales_agents', 'executive_agents', 'sales_agent_commissions'];
    }

    protected function artisanCommand(): string
    {
        return 'import:sales-agent-commissions';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);
        if (! str_contains($name, 'COMMISSION')) {
            return false;
        }

        return str_contains($name, 'AGENT') || str_contains($name, 'SALES AGENT');
    }

    public function listSheets(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            $u = strtoupper(trim($name));
            $fullMonths = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];
            $isMonth = in_array($u, $fullMonths, true);

            $header = $isMonth ? $this->findCommissionHeader($ws) : null;
            $supported = $isMonth && $header !== null;
            $rows = $supported
                ? ExcelSheetHelper::countNonEmptyRows(
                    $ws,
                    $header['row'],
                    ExcelSheetHelper::col($header['map'], ['PLATE NUMBER', 'PLATE', 'UNIT'])
                )
                : 0;

            $out[] = [
                'name' => $name,
                'supported' => $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported ? null : 'Month sheets (JANUARY–DECEMBER) with PLATE/UNIT/SHOWROOM headers only.',
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
            $header = $this->findCommissionHeader($ws);
            if (! $header) {
                continue;
            }
            $map = $header['map'];
            $cShow = ExcelSheetHelper::col($map, ['SHOWROOM']);
            $cYear = ExcelSheetHelper::col($map, ['YEAR']);
            $cMake = ExcelSheetHelper::col($map, ['MAKE']);
            $cModel = ExcelSheetHelper::col($map, ['MODEL']);
            $cPlate = ExcelSheetHelper::col($map, ['PLATE NUMBER', 'PLATE']);
            $cUnit = ExcelSheetHelper::col($map, ['UNIT']);
            $cTxn = ExcelSheetHelper::col($map, ['TRANSACTION TYPE', 'TRANSACTION']);
            $cSource = ExcelSheetHelper::col($map, ['SOURCE']);
            $cRes = ExcelSheetHelper::col($map, ['RESERVATION DATE', 'RESERVATION']);
            $cRel = ExcelSheetHelper::col($map, ['RELEASE DATE', 'RELEASE']);
            $cSales = ExcelSheetHelper::col($map, ['SALES PERSON', 'SALES']);
            $cAmount = ExcelSheetHelper::col($map, ['COMS AMOUNT', 'COMMISSION', 'AMOUNT']);
            $cPay = ExcelSheetHelper::col($map, ['DATE OF PAYMENT', 'PAYMENT DATE']);
            $cSent = ExcelSheetHelper::col($map, ['DATE SENT', 'DATE SENT TO', 'SENT']);
            $cStatus = ExcelSheetHelper::col($map, ['COMMISSION STATUS', 'STATUS', 'BOOM']);
            $cNotes = ExcelSheetHelper::col($map, ['NOTES', 'REMARKS']);

            $highest = (int) $ws->getHighestDataRow();
            for ($r = $header['row'] + 1; $r <= $highest; $r++) {
                $plate = ExcelSheetHelper::normalizePlate(ExcelSheetHelper::cellString($ws, $cPlate, $r));
                $source = ExcelSheetHelper::cellString($ws, $cSource, $r);
                $make = ExcelSheetHelper::cellString($ws, $cMake, $r);
                $model = ExcelSheetHelper::cellString($ws, $cModel, $r);
                $year = ExcelSheetHelper::cell($ws, $cYear, $r);
                if ($plate === '' && ! $make && ! $source) {
                    continue;
                }
                if ($plate === '') {
                    continue;
                }

                $yearVal = null;
                if (is_numeric($year)) {
                    $yearVal = (int) $year;
                } elseif (is_string($year) && trim($year) !== '') {
                    $yearVal = trim($year);
                }

                $agentNames = $this->parseAgentNames($source);
                $amount = ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cAmount, $r));
                $payDate = ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cPay, $r));
                $sentDate = ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cSent, $r)) ?: $payDate;
                $status = ExcelSheetHelper::cellString($ws, $cStatus, $r);
                if (! $status) {
                    $status = $payDate ? 'Posted' : 'Pending';
                }

                $unit = ExcelSheetHelper::cellString($ws, $cUnit, $r);
                if (! $unit && ($yearVal || $make || $model)) {
                    $unit = trim(implode(' ', array_filter([(string) $yearVal, $make, $model])));
                }

                $rows[] = [
                    'row' => $r,
                    'sheet' => $name,
                    'showroom' => ExcelSheetHelper::cellString($ws, $cShow, $r),
                    'year' => $yearVal,
                    'make' => $make,
                    'model' => $model,
                    'plate_number' => $plate,
                    'unit' => $unit,
                    'transaction_type' => ExcelSheetHelper::cellString($ws, $cTxn, $r),
                    'source' => $source,
                    'agent_names' => $agentNames,
                    'reservation_date' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cRes, $r)),
                    'release_date' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cRel, $r)),
                    'sales_person' => ExcelSheetHelper::cellString($ws, $cSales, $r),
                    'amount' => $amount,
                    'agents_folder_amount' => null,
                    'sales_executive_commission' => null,
                    'se_names' => [],
                    'proof_of_appointment' => null,
                    'sign_client_with_agent' => null,
                    'date_of_payment' => $payDate,
                    'date_sent' => $sentDate,
                    'commission_status' => $status,
                    'notes' => ExcelSheetHelper::cellString($ws, $cNotes, $r) ?: $source,
                ];
            }
        }
        $ss->disconnectWorksheets();

        return $rows;
    }

    /** @return array{row:int,map:array<string,int>}|null */
    private function findCommissionHeader($ws): ?array
    {
        // Scan more rows (up to 25) for PLATE / UNIT / SHOWROOM
        return ExcelSheetHelper::findHeaderMap($ws, ['PLATE NUMBER', 'PLATE', 'UNIT', 'SHOWROOM'], 25, 40);
    }

    /** @return array<int, string> */
    private function parseAgentNames(?string $source): array
    {
        if ($source === null || trim($source) === '') {
            return [];
        }
        $names = [];
        if (preg_match_all('/AGENT\s*:\s*([^|;\/]+)/i', $source, $m)) {
            foreach ($m[1] as $name) {
                $n = trim($name);
                if ($n !== '') {
                    $names[] = $n;
                }
            }
        }
        if ($names === [] && stripos($source, 'AGENT') !== false) {
            $n = trim(preg_replace('/^.*AGENT\s*:?\s*/i', '', $source));
            if ($n !== '') {
                $names[] = $n;
            }
        }

        return array_values(array_unique($names));
    }
}
