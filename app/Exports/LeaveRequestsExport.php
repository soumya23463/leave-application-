<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeaveRequestsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected ?int $employeeId = null,
        protected ?string $fromDate = null,
        protected ?string $toDate = null,
        protected ?string $status = null,
    ) {}

    public function query()
    {
        $query = LeaveRequest::with(['employee']);

        if ($this->employeeId) {
            $query->where('user_id', $this->employeeId);
        }
        if ($this->fromDate) {
            $query->whereDate('from_date', '>=', $this->fromDate);
        }
        if ($this->toDate) {
            $query->whereDate('to_date', '<=', $this->toDate);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Employee', 'From', 'To', 'Days', 'Reason', 'Status', 'Applied On'];
    }

    public function map($row): array
    {
        return [
            $row->employee?->name,
            $row->from_date?->format('d M Y'),
            $row->to_date?->format('d M Y'),
            $row->days_requested,
            $row->reason,
            ucfirst($row->status),
            $row->created_at?->format('d M Y'),
        ];
    }
}
