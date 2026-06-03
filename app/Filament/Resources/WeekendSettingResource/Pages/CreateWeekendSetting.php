<?php

namespace App\Filament\Resources\WeekendSettingResource\Pages;

use App\Filament\Resources\WeekendSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWeekendSetting extends CreateRecord
{
    protected static string $resource = WeekendSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
