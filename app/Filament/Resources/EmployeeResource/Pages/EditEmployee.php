<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use Filament\Actions;
use App\Filament\Resources\EmployeeResource;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
    protected function getSavedNotificationTitle(): ?string
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return 'Admin updated successfully';
        }
        return 'Employee updated successfully';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
