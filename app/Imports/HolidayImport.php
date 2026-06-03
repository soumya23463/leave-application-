<?php

namespace App\Imports;

use App\Models\Holiday;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class HolidayImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row): ?Holiday
    {
        $raw = trim((string) $row['date']);

        if (empty($raw)) return null;

        $date = $this->parseDate($raw);

        if (!$date) return null;

        $name = trim($row['occassion'] ?? '');
        if (empty($name)) return null;

        return Holiday::firstOrCreate(
            ['date' => $date],
            [
                'name'        => $name,
                'date'        => $date,
                'description' => null,
                'status'      => true,
            ]
        );
    }

    private function parseDate(string $raw): ?string
    {
        // Excel serial number (e.g. 45678)
        if (is_numeric($raw)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $raw))->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        // String formats
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'd-m-y'] as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $raw);
                if ($parsed && $parsed->format($format) === $raw) {
                    return $parsed->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }
}
