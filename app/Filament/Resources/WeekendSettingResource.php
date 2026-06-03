<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\WeekendSetting;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use App\Filament\Resources\WeekendSettingResource\Pages;

class WeekendSettingResource extends Resource
{
    protected static ?string $model = WeekendSetting::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static string|\UnitEnum|null $navigationGroup = 'Master';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Weekend Settings';

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            CheckboxList::make('days')
                ->options([
                    'Monday' => 'Monday',
                    'Tuesday' => 'Tuesday',
                    'Wednesday' => 'Wednesday',
                    'Thursday' => 'Thursday',
                    'Friday' => 'Friday',
                    'Saturday' => 'Saturday',
                    'Sunday' => 'Sunday',
                ])
                ->required()
                ->columns(4),
            DatePicker::make('effective_date')
                ->required()
                ->default(now())
                ->native(false)
                ->displayFormat('d F Y'),
            Toggle::make('status')
                ->default(true)
                ->label('Active'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('days')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),
                Tables\Columns\TextColumn::make('effective_date')->date('d M Y')->sortable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
            ])
            ->actions(
                auth()->user()?->isAdmin() ? [
                    \Filament\Actions\EditAction::make(),
                    \Filament\Actions\DeleteAction::make(),
                ] : []
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWeekendSettings::route('/'),
            'create' => Pages\CreateWeekendSetting::route('/create'),
            'edit' => Pages\EditWeekendSetting::route('/{record}/edit'),
        ];
    }
}
