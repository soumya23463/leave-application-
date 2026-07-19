<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\WeekendSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeamCalendarController extends Controller
{
    /**
     * Month calendar showing everyone's approved leaves + holidays.
     */
    public function index(Request $request)
    {
        // Which month to show (?month=YYYY-MM), default: current month.
        try {
            $monthStart = $request->filled('month')
                ? Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
                : now()->startOfMonth();
        } catch (\Throwable $e) {
            $monthStart = now()->startOfMonth();
        }

        $monthEnd = $monthStart->copy()->endOfMonth();

        // Full weeks (Sun–Sat) covering the month.
        $gridStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $gridEnd   = $monthEnd->copy()->endOfWeek(Carbon::SATURDAY);

        $weekendDays = WeekendSetting::activeWeekendDays();

        // Only super admins can filter across all departments; admins & employees are locked to their own.
        $canSeeAll = isSuperAdmin();
        $departmentId = $canSeeAll
            ? ($request->query('department') ?: null)
            : authUser()->department_id;

        $leaves = LeaveRequest::with('employee.department')
            ->where('status', 'approved')
            ->when(! $canSeeAll || $departmentId, fn ($q) => $q->whereHas('employee',
                fn ($e) => $e->where('department_id', $departmentId)))
            ->whereDate('from_date', '<=', $gridEnd->toDateString())
            ->whereDate('to_date', '>=', $gridStart->toDateString())
            ->get();

        $holidays = Holiday::where('status', true)
            ->whereBetween('date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->get()
            ->keyBy(fn ($h) => $h->date->toDateString());

        // Build the week/day grid.
        $weeks  = [];
        $cursor = $gridStart->copy();
        while ($cursor <= $gridEnd) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $ds = $cursor->toDateString();

                $dayLeaves = $leaves->filter(
                    fn ($l) => $l->from_date->toDateString() <= $ds
                            && $l->to_date->toDateString() >= $ds
                )->values();

                $week[] = [
                    'date'      => $cursor->copy(),
                    'inMonth'   => $cursor->month === $monthStart->month,
                    'isToday'   => $cursor->isToday(),
                    'isWeekend' => in_array($cursor->dayName, $weekendDays, true),
                    'holiday'   => $holidays->get($ds),
                    'leaves'    => $dayLeaves,
                ];

                $cursor->addDay();
            }
            $weeks[] = $week;
        }

        return view('team-calendar.index', [
            'monthLabel'         => $monthStart->format('F Y'),
            'prevMonth'          => $monthStart->copy()->subMonth()->format('Y-m'),
            'nextMonth'          => $monthStart->copy()->addMonth()->format('Y-m'),
            'thisMonth'          => now()->format('Y-m'),
            'weeks'              => $weeks,
            'departments'        => Department::orderBy('name')->get(),
            'selectedDepartment' => $departmentId,
            'showFilter'         => $canSeeAll,
            'ownDeptName'        => $canSeeAll ? null : (authUser()->department?->name ?? 'No department'),
        ]);
    }
}
