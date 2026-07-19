<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return isAdmin() ? $this->adminDashboard() : $this->employeeDashboard();
    }

    protected function adminDashboard()
    {
        $today = now()->toDateString();
        $year = now()->year;

        $employeeRequests = LeaveRequest::whereHas('employee', fn ($q) => $q->where('role', 'employee'));

        $stats = [
            'active_employees' => User::where('role', 'employee')->where('status', 'active')->count(),
            'yearly_holidays'  => Holiday::where('status', true)->whereYear('date', $year)->count(),
            'remaining_holidays' => Holiday::where('status', true)->whereYear('date', $year)->whereDate('date', '>=', $today)->count(),
            'total_requests'   => (clone $employeeRequests)->count(),
            'pending_requests' => (clone $employeeRequests)->where('status', 'pending')->count(),
            'approved_requests' => (clone $employeeRequests)->where('status', 'approved')->count(),
        ];

        $onLeaveToday = $this->onLeaveToday();

        // Monthly leave counts for the trailing 12 months
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->startOfMonth()->subMonths($i);
            $count = LeaveRequest::whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->count();
            $months->push(['label' => $m->format('M Y'), 'count' => $count]);
        }

        return view('dashboard.admin', [
            'stats' => $stats,
            'onLeaveToday' => $onLeaveToday,
            'employeesOnLeave' => $onLeaveToday->where('employee.role', 'employee')->values(),
            'adminsOnLeave' => $onLeaveToday->where('employee.role', 'admin')->values(),
            'chartLabels' => $months->pluck('label'),
            'chartData' => $months->pluck('count'),
        ]);
    }

    protected function employeeDashboard()
    {
        $userId = authId();
        $year = now()->year;
        $today = now()->toDateString();

        $balances = LeaveBalance::where('employee_id', $userId)->where('year', $year);

        $stats = [
            'remaining_leaves' => (clone $balances)->sum('remaining_days'),
            'used_leaves'      => (clone $balances)->sum('used_days'),
            'pending_requests' => LeaveRequest::where('user_id', $userId)->where('status', 'pending')->count(),
            'upcoming_holidays' => Holiday::where('status', true)->whereDate('date', '>=', $today)->count(),
        ];

        $recentRequests = LeaveRequest::where('user_id', $userId)->latest()->limit(5)->get();

        $upcomingHolidays = Holiday::where('status', true)
            ->whereDate('date', '>=', $today)
            ->orderBy('date')
            ->limit(5)
            ->get();

        $onLeaveToday = $this->onLeaveToday();

        return view('dashboard.employee', [
            'stats'            => $stats,
            'recentRequests'   => $recentRequests,
            'upcomingHolidays' => $upcomingHolidays,
            'employeesOnLeave' => $onLeaveToday->where('employee.role', 'employee')->values(),
            'adminsOnLeave'    => $onLeaveToday->where('employee.role', 'admin')->values(),
        ]);
    }

    /**
     * Everyone (employees + admins) whose approved leave covers today.
     */
    protected function onLeaveToday()
    {
        $today = now()->toDateString();

        return LeaveRequest::with('employee')
            ->where('status', 'approved')
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->get();
    }
}
