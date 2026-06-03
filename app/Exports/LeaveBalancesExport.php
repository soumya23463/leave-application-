<?php

namespace App\Exports;

use App\Models\LeaveBalance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeaveBalancesExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected ?int $year = null,
        protected ?int $employeeId = null,
    ) {}

    public function query()
    {
        $query = LeaveBalance::with(['employee']);

        if ($this->year) {
            $query->where('year', $this->year);
        }
        if ($this->employeeId) {
            $query->where('employee_id', $this->employeeId);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Employee', 'Year', 'Total Days', 'Used Days', 'Remaining Days', 'Carried Forward'];
    }

    public function map($row): array
    {
        return [
            $row->employee?->name,
            $row->year,
            $row->total_days,
            $row->used_days,
            $row->remaining_days,
            $row->carried_forward,
        ];
    }
}
