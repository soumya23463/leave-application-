<?php

namespace App\Filament\Resources\WeekendSettingResource\Pages;

use App\Filament\Resources\WeekendSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeekendSetting extends EditRecord
{
    protected static string $resource = WeekendSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
