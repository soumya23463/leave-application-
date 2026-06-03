<?php

namespace App\Filament\Resources\AdminLeaveResource\Pages;

use App\Filament\Resources\AdminLeaveResource;
use App\Models\LeaveRequest;
use App\Services\LeaveCalculator;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAdminLeave extends CreateRecord
{
    protected static string $resource = AdminLeaveResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['days_requested'] = LeaveCalculator::calculate($data['from_date'], $data['to_date']);
        $data['status']         = 'approved';
        $data['approved_by']    = auth()->id();
        $data['approved_at']    = now();

        return LeaveRequest::create($data);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Admin leave created successfully';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
