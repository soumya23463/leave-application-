<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLeaveRequest;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveCalculator;
use Illuminate\Http\Request;

class AdminLeaveController extends Controller
{
    /**
     * This resource records leave for admin-role users. Every admin leave is
     * auto-approved on create.
     */
    public function index(Request $request)
    {
        $leaveRequests = LeaveRequest::with(['employee', 'approvedBy'])
            ->whereHas('employee', fn ($q) => $q->where('role', 'admin'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin-leaves.index', compact('leaveRequests'));
    }

    public function create()
    {
        return view('admin-leaves.create', array_merge(
            ['employees' => $this->adminUsers()],
            $this->previewData(),
        ));
    }

    public function store(AdminLeaveRequest $request)
    {
        $data = $request->validated();

        $days = LeaveCalculator::calculate($data['from_date'], $data['to_date']);

        LeaveRequest::create([
            'user_id'        => $data['user_id'],
            'from_date'      => $data['from_date'],
            'to_date'        => $data['to_date'],
            'reason'         => $data['reason'],
            'days_requested' => $days,
            'status'         => 'approved',
            'approved_by'    => authId(),
            'approved_at'    => now(),
        ]);

        // Observer fires the Discord DM automatically.

        return redirect()->route('admin-leaves.index')->with('success', 'Admin leave recorded.');
    }

    public function show(LeaveRequest $adminLeave)
    {
        $adminLeave->load(['employee', 'approvedBy']);

        return view('admin-leaves.show', ['leaveRequest' => $adminLeave]);
    }

    public function edit(LeaveRequest $adminLeave)
    {
        return view('admin-leaves.edit', array_merge(
            ['leaveRequest' => $adminLeave, 'employees' => $this->adminUsers()],
            $this->previewData(),
        ));
    }

    public function update(AdminLeaveRequest $request, LeaveRequest $adminLeave)
    {
        $data = $request->validated();

        $adminLeave->update([
            'user_id'        => $data['user_id'],
            'from_date'      => $data['from_date'],
            'to_date'        => $data['to_date'],
            'reason'         => $data['reason'],
            'days_requested' => LeaveCalculator::calculate($data['from_date'], $data['to_date']),
            'status'         => 'approved',
        ]);

        return redirect()->route('admin-leaves.index')->with('success', 'Admin leave updated.');
    }

    public function destroy(LeaveRequest $adminLeave)
    {
        $adminLeave->delete();

        return redirect()->route('admin-leaves.index')->with('success', 'Admin leave deleted.');
    }

    // ---- helpers ----

    protected function adminUsers()
    {
        return User::where('role', 'admin')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    /**
     * Data used by the client-side working-day preview.
     */
    protected function previewData(): array
    {
        return [
            'weekendDays' => \App\Models\WeekendSetting::activeWeekendDays(),
            'holidayDates' => \App\Models\Holiday::where('status', true)
                ->pluck('date')
                ->map(fn ($d) => \Carbon\Carbon::parse($d)->toDateString())
                ->values(),
        ];
    }
}
