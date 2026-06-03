<?php

namespace App\Filament\Resources\AdminLeaveResource\Pages;

use App\Filament\Resources\AdminLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminLeave extends EditRecord
{
    protected static string $resource = AdminLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Admin leave updated successfully';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
