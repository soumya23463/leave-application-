<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Filament\Resources\HolidayResource;
use App\Imports\HolidayImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListHolidays extends ListRecords
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        if (!auth()->user()?->isAdmin()) {
            return [];
        }

        return [
            Action::make('import')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('CSV / Excel File')
                        ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/plain', 'application/octet-stream'])
                        ->required()
                        ->storeFiles(false),
                ])
                ->action(function (array $data) {
                    Excel::import(new HolidayImport, $data['file']);
                    Notification::make()
                        ->title('Holidays imported successfully')
                        ->success()
                        ->send();
                }),

            Action::make('download_sample')
                ->label('Sample CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $csv = "Date,Occassion\n";
                    $csv .= "01-01-2026,New Year's Day\n";
                    $csv .= "26/01/2026,Republic Day\n";
                    $csv .= "25/12/2026,Christmas\n";

                    return response()->streamDownload(
                        fn () => print($csv),
                        'holidays_sample.csv',
                        ['Content-Type' => 'text/csv']
                    );
                }),

            Actions\CreateAction::make(),
        ];
    }
}
