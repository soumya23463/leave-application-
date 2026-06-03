<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\LeaveRequest;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class EmployeesOnLeaveWidget extends BaseWidget
{
    protected static ?string $heading = 'Employees Currently On Leave';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LeaveRequest::with(['employee'])
                    ->where('status', 'approved')
                    ->whereDate('from_date', '<=', now())
                    ->whereDate('to_date', '>=', now())
            )
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')->label('Employee')
                    ->formatStateUsing(fn ($state, $record) => $state . ' (' . ucfirst($record->employee?->role) . ')'),
                Tables\Columns\TextColumn::make('from_date')->date('d M Y')->label('From'),
                Tables\Columns\TextColumn::make('to_date')->date('d M Y')->label('To'),
                Tables\Columns\TextColumn::make('days_requested')->label('Days'),
            ]);
    }
}
