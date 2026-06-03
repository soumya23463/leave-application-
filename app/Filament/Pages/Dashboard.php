<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminStatsOverview;
use App\Filament\Widgets\EmployeeStatsOverview;
use App\Filament\Widgets\EmployeesOnLeaveWidget;
use App\Filament\Widgets\MonthlyLeaveChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        if (auth()->user()?->isAdmin()) {
            return [
                AdminStatsOverview::class,
                MonthlyLeaveChart::class,
                EmployeesOnLeaveWidget::class,
            ];
        }

        return [
            EmployeeStatsOverview::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
