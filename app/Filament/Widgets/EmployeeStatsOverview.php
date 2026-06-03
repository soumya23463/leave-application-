<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('employee') ?? false;
    }

    protected function getStats(): array
    {
        $userId = auth()->id();
        $year = now()->year;

        $balances = LeaveBalance::where('employee_id', $userId)->where('year', $year)->get();


        return [
            Stat::make('Remaining Leaves', $balances->sum('remaining_days'))
                ->icon('heroicon-o-calendar')
                ->color('success'),
            Stat::make('Used Leaves', $balances->sum('used_days'))
                ->icon('heroicon-o-check-circle')
                ->color('primary'),
            Stat::make('Pending Requests', LeaveRequest::where('user_id', $userId)->where('status', 'pending')->count())
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Upcoming Holidays', Holiday::where('status', true)->where('date', '>=', now())->count())
                ->icon('heroicon-o-calendar-days')
                ->color('info'),
        ];
    }
}
