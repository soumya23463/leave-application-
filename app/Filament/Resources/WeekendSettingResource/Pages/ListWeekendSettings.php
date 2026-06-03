<?php

namespace App\Filament\Resources\WeekendSettingResource\Pages;

use Filament\Actions;
use App\Filament\Resources\WeekendSettingResource;
use Filament\Resources\Pages\ListRecords;

class ListWeekendSettings extends ListRecords
{
    protected static string $resource = WeekendSettingResource::class;

    protected function getHeaderActions(): array
    {
        if (!auth()->user()?->isAdmin()) {
            return [];
        }
        return [Actions\CreateAction::make()];
    }
    
}
