<?php

namespace App\Filament\Resources\AdminLeaveResource\Pages;

use App\Filament\Resources\AdminLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminLeaves extends ListRecords
{
    protected static string $resource = AdminLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
