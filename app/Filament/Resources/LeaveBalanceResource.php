<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveBalanceResource\Pages;
use App\Models\LeaveBalance;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveBalanceResource extends Resource
{
    protected static ?string $model = LeaveBalance::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';
    protected static string|\UnitEnum|null $navigationGroup = 'Leave Management';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('employee_id')
                ->label('Employee')
                ->options(fn () => User::where('role', 'employee')->pluck('name', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('total_days')->numeric()->required(),
            TextInput::make('used_days')->numeric()->default(0),
            TextInput::make('remaining_days')->numeric()->default(0),
            TextInput::make('carried_forward')->numeric()->default(0),
            TextInput::make('year')->numeric()->default(now()->year)->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                if (auth()->user()?->hasRole('employee')) {
                    $query->where('employee_id', auth()->id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('total_days')->label('Total'),
                Tables\Columns\TextColumn::make('used_days')->label('Used'),
                Tables\Columns\TextColumn::make('remaining_days')->label('Remaining'),
                Tables\Columns\TextColumn::make('carried_forward')->label('Carry Fwd'),
                Tables\Columns\TextColumn::make('year')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->options(fn () => LeaveBalance::distinct()->pluck('year', 'year')->toArray()),
            ])
            ->actions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveBalances::route('/'),
        ];
    }
}
