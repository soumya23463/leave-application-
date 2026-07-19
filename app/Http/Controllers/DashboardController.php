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

        // Regular admins see only their own department; super admins see everyone.
        $scoped = ! isSuperAdmin();
        $deptId = authUser()->department_id;

        $employeeRequests = LeaveRequest::whereHas('employee', function ($q) use ($scoped, $deptId) {
            $q->where('role', 'employee');
            if ($scoped) {
                $q->where('department_id', $deptId);
            }
        });

        $stats = [
            'active_employees' => User::where('role', 'employee')->where('status', 'active')
                ->when($scoped, fn ($q) => $q->where('department_id', $deptId))->count(),
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
                ->when($scoped, fn ($q) => $q->whereHas('employee',
                    fn ($e) => $e->where('department_id', $deptId)))
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
     * People whose approved leave covers today. Super admins see everyone;
     * everyone else sees only their own department.
     */
    protected function onLeaveToday()
    {
        $today = now()->toDateString();

        return LeaveRequest::with('employee')
            ->where('status', 'approved')
            ->when(! isSuperAdmin(), fn ($q) => $q->whereHas('employee',
                fn ($e) => $e->where('department_id', authUser()->department_id)))
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->get();
    }
}
