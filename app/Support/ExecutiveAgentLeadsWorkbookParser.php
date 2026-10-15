<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExecutiveAgentLeadsWorkbookParser
{
    /**
     * @return list<array<string, mixed>>
     */
    public function parse(string $path): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $ss = $reader->load($path);
        $rows = [];

        foreach ($ss->getWorksheetIterator() as $ws) {
            foreach ($this->parseSheet($ws) as $row) {
                $rows[] = $row;
            }
        }

        $ss->disconnectWorksheets();

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function parseSheet(Worksheet $ws): array
    {
        $highest = (int) $ws->getHighestDataRow();
        if ($highest < 3) {
            return [];
        }

        $sheet = $ws->getTitle();
        $out = [];

        // Row 1 = group headers, row 2 = sub-headers, data from row 3.
        for ($r = 3; $r <= $highest; $r++) {
            $customer = $this->str($ws->getCellByColumnAndRow(3, $r)->getValue());
            if ($customer === '') {
                continue;
            }

            $out[] = [
                'application_date' => $this->toDate($ws->getCellByColumnAndRow(1, $r)->getValue()),
                'application_source' => $this->str($ws->getCellByColumnAndRow(2, $r)->getValue(), 100),
                'customer_name' => mb_substr($customer, 0, 255),
                'customer_phone' => $this->str($ws->getCellByColumnAndRow(4, $r)->getValue(), 100),
                'unit_inquired' => $this->str($ws->getCellByColumnAndRow(5, $r)->getValue(), 255),
                'notes' => $this->str($ws->getCellByColumnAndRow(6, $r)->getValue()),
                'about_what' => $this->str($ws->getCellByColumnAndRow(7, $r)->getValue(), 255),
                'followup_1_exec' => $this->str($ws->getCellByColumnAndRow(9, $r)->getValue(), 100),
                'followup_1_date' => $this->toDate($ws->getCellByColumnAndRow(10, $r)->getValue()),
                'followup_1_outcome' => $this->str($ws->getCellByColumnAndRow(11, $r)->getValue(), 255),
                'followup_1_notes' => $this->str($ws->getCellByColumnAndRow(12, $r)->getValue()),
                'followup_2_exec' => $this->str($ws->getCellByColumnAndRow(14, $r)->getValue(), 100),
                'followup_2_date' => $this->toDate($ws->getCellByColumnAndRow(15, $r)->getValue()),
                'followup_2_outcome' => $this->str($ws->getCellByColumnAndRow(16, $r)->getValue(), 255),
                'followup_2_notes' => $this->str($ws->getCellByColumnAndRow(17, $r)->getValue()),
                'followup_3_exec' => $this->str($ws->getCellByColumnAndRow(19, $r)->getValue(), 100),
                'followup_3_date' => $this->toDate($ws->getCellByColumnAndRow(20, $r)->getValue()),
                'followup_3_outcome' => $this->str($ws->getCellByColumnAndRow(21, $r)->getValue(), 255),
                'followup_3_notes' => $this->str($ws->getCellByColumnAndRow(22, $r)->getValue()),
                'followup_4_exec' => $this->str($ws->getCellByColumnAndRow(24, $r)->getValue(), 100),
                'followup_4_date' => $this->toDate($ws->getCellByColumnAndRow(25, $r)->getValue()),
                'followup_4_outcome' => $this->str($ws->getCellByColumnAndRow(26, $r)->getValue(), 255),
                'followup_4_notes' => $this->str($ws->getCellByColumnAndRow(27, $r)->getValue()),
                'source_sheet' => mb_substr($sheet, 0, 100),
                'source_row' => $r,
            ];
        }

        return $out;
    }

    private function str(mixed $value, ?int $max = null): string
    {
        if ($value === null) {
            return '';
        }
        $s = trim((string) $value);
        if ($max !== null && $s !== '') {
            return mb_substr($s, 0, $max);
        }

        return $s;
    }

    private function toDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_numeric($value)) {
            $n = (float) $value;
            if ($n < 30000 || $n > 60000) {
                return null;
            }
            try {
                return ExcelDate::excelToDateTimeObject($n)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }
        $s = trim((string) $value);
        $ts = strtotime($s);

        return $ts === false ? null : date('Y-m-d', $ts);
    }
}
