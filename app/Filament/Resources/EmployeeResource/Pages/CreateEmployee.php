<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Models\LeaveBalance;
use App\Models\User;
use App\Filament\Resources\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = User::create($data);

        LeaveBalance::create([
            'employee_id'     => $user->id,
            'total_days'      => 24,
            'used_days'       => 0,
            'remaining_days'  => 24,
            'carried_forward' => 0,
            'year'            => now()->year,
        ]);

        return $user;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return auth()->user()?->isAdmin()
            ? 'Admin created successfully'
            : 'Employee created successfully';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
