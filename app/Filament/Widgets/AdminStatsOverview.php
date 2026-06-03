<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    protected function getStats(): array
    {
        $onLeaveToday = LeaveRequest::where('status', 'approved')
            ->whereDate('from_date', '<=', now())
            ->whereDate('to_date', '>=', now())
            ->count();

        return [
            Stat::make('Total Employees', User::where('role', 'employee')->where('status', 'active')->count())
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('Yearly Holidays', Holiday::where('status', true)->whereYear('date', now()->year)->count())
                ->icon('heroicon-o-calendar-days')
                ->color('success'),
            Stat::make('Remaining Holidays', Holiday::where('status', true)->whereYear('date', now()->year)
        ->whereDate('date', '>=', now())->count())
                ->icon('heroicon-o-calendar-days')
                ->color('success'),
            Stat::make('Total Requests', LeaveRequest::whereHas('employee', fn ($q) => $q->where('role', 'employee'))->count())
                ->icon('heroicon-o-document-text')
                ->color('info'),
            Stat::make('Pending Requests', LeaveRequest::whereHas('employee', fn ($q) => $q->where('role', 'employee'))->where('status', 'pending')->count())
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Approved Requests', LeaveRequest::whereHas('employee', fn ($q) => $q->where('role', 'employee'))->where('status', 'approved')->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('On Leave Today', $onLeaveToday)
                ->icon('heroicon-o-user-minus')
                ->color('danger'),
        ];
    }
}
