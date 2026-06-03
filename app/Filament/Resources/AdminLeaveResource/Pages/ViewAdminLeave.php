<?php

namespace App\Filament\Resources\AdminLeaveResource\Pages;

use App\Filament\Resources\AdminLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAdminLeave extends ViewRecord
{
    protected static string $resource = AdminLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
