<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeaveRequestRequest;
use App\Http\Requests\RejectLeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\GenericNotification;
use App\Services\LeaveCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class LeaveRequestController extends Controller
{
    /**
     * This resource manages leave for employee-role users.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employee', 'approvedBy'])
            ->whereHas('employee', fn ($q) => $q->where('role', 'employee'))
            ->latest();

        if (! isAdmin()) {
            // Employees see only their own requests.
            $query->where('user_id', authId());
        } elseif (! isSuperAdmin()) {
            // Regular admins see only their own department.
            $deptId = authUser()->department_id;
            $query->whereHas('employee', fn ($q) => $q->where('department_id', $deptId));
        }

        if (isAdmin() && $request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->paginate(15)->withQueryString();

        return view('leave-requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        $employees = isAdmin()
            ? User::where('role', 'employee')->where('status', 'active')
                ->when(! isSuperAdmin(), fn ($q) => $q->where('department_id', authUser()->department_id))
                ->orderBy('name')->get()
            : collect();

        return view('leave-requests.create', array_merge(
            compact('employees'),
            $this->previewData(),
        ));
    }

    public function store(LeaveRequestRequest $request)
    {
        $data = $request->validated();

        $userId = isAdmin() ? ($data['user_id'] ?? authId()) : authId();

        $this->assertCanFileFor($userId);
        $this->assertNoOverlap($userId, $data['from_date'], $data['to_date']);

        $days = LeaveCalculator::calculate($data['from_date'], $data['to_date']);

        $leave = LeaveRequest::create([
            'user_id'        => $userId,
            'from_date'      => $data['from_date'],
            'to_date'        => $data['to_date'],
            'reason'         => $data['reason'],
            'days_requested' => $days,
            'status'         => isAdmin() ? 'approved' : 'pending',
            'approved_by'    => isAdmin() ? authId() : null,
            'approved_at'    => isAdmin() ? now() : null,
        ]);

        // Notify all admins when an employee submits a request
        if (! isAdmin()) {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new GenericNotification(
                'New leave request',
                "{$leave->employee->name} requested leave ({$days} day(s)).",
                route('leave-requests.show', $leave),
            ));
        }

        return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $this->authorizeView($leaveRequest);
        $leaveRequest->load(['employee', 'approvedBy']);

        return view('leave-requests.show', compact('leaveRequest'));
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnership($leaveRequest);

        abort_if($leaveRequest->status !== 'pending', 403, 'Only pending requests can be edited.');

        $employees = isAdmin()
            ? User::where('role', 'employee')->where('status', 'active')
                ->when(! isSuperAdmin(), fn ($q) => $q->where('department_id', authUser()->department_id))
                ->orderBy('name')->get()
            : collect();

        return view('leave-requests.edit', array_merge(
            compact('leaveRequest', 'employees'),
            $this->previewData(),
        ));
    }

    public function update(LeaveRequestRequest $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnership($leaveRequest);
        abort_if($leaveRequest->status !== 'pending', 403, 'Only pending requests can be edited.');

        $data = $request->validated();

        $userId = isAdmin() ? ($data['user_id'] ?? $leaveRequest->user_id) : authId();

        $this->assertCanFileFor($userId);
        $this->assertNoOverlap($userId, $data['from_date'], $data['to_date'], $leaveRequest->id);

        $leaveRequest->update([
            'user_id'        => $userId,
            'from_date'      => $data['from_date'],
            'to_date'        => $data['to_date'],
            'reason'         => $data['reason'],
            'days_requested' => LeaveCalculator::calculate($data['from_date'], $data['to_date']),
        ]);

        return redirect()->route('leave-requests.index')->with('success', 'Leave request updated.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnership($leaveRequest);
        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted.');
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        abort_unless(isAdmin(), 403);
        $this->authorizeDepartment($leaveRequest);

        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'This request is not pending.');
        }

        $days = LeaveCalculator::calculate(
            $leaveRequest->from_date->toDateString(),
            $leaveRequest->to_date->toDateString(),
        );

        $leaveRequest->update([
            'status'         => 'approved',
            'days_requested' => $days,
            'approved_by'    => authId(),
            'approved_at'    => now(),
        ]);

        // Decrement the matching yearly balance
        $balance = LeaveBalance::where('employee_id', $leaveRequest->user_id)
            ->where('year', $leaveRequest->from_date->year)
            ->first();

        if ($balance) {
            $balance->used_days += $days;
            $balance->remaining_days -= $days;
            $balance->save();
        }

        $leaveRequest->employee?->notify(new GenericNotification(
            'Leave approved',
            "Your leave from {$leaveRequest->from_date->format('d M Y')} to {$leaveRequest->to_date->format('d M Y')} was approved.",
            route('leave-requests.show', $leaveRequest),
        ));

        return back()->with('success', 'Leave request approved.');
    }

    public function reject(RejectLeaveRequest $request, LeaveRequest $leaveRequest)
    {
        abort_unless(isAdmin(), 403);
        $this->authorizeDepartment($leaveRequest);

        $data = $request->validated();

        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'This request is not pending.');
        }

        $leaveRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $data['rejection_reason'],
            'approved_by'      => authId(),
            'approved_at'      => now(),
        ]);

        $leaveRequest->employee?->notify(new GenericNotification(
            'Leave rejected',
            "Your leave request was rejected. Reason: {$data['rejection_reason']}",
            route('leave-requests.show', $leaveRequest),
        ));

        return back()->with('success', 'Leave request rejected.');
    }

    // ---- helpers ----

    protected function assertNoOverlap(int $userId, string $from, string $to, ?int $ignoreId = null): void
    {
        $exists = LeaveRequest::where('user_id', $userId)
            ->where('status', '!=', 'rejected')
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('from_date', [$from, $to])
                    ->orWhereBetween('to_date', [$from, $to])
                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->where('from_date', '<=', $from)->where('to_date', '>=', $to);
                    });
            })
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'from_date' => 'This date range overlaps an existing leave request.',
            ]);
        }
    }

    protected function authorizeOwnership(LeaveRequest $leaveRequest): void
    {
        if (! isAdmin()) {
            if ($leaveRequest->user_id !== authId()) {
                abort(403);
            }
            return;
        }

        // Regular admins are limited to their own department.
        $this->authorizeDepartment($leaveRequest);
    }

    protected function authorizeDepartment(LeaveRequest $leaveRequest): void
    {
        if (isAdmin() && ! isSuperAdmin()
            && $leaveRequest->employee?->department_id !== authUser()->department_id) {
            abort(403, 'You can only manage leave for your own department.');
        }
    }

    /**
     * A regular admin can only file/edit leave for employees in their own department.
     */
    protected function assertCanFileFor(int $userId): void
    {
        if (! isAdmin() || isSuperAdmin() || $userId === authId()) {
            return;
        }

        $target = User::find($userId);
        if (! $target || $target->department_id !== authUser()->department_id) {
            abort(403, 'You can only file leave for your own department.');
        }
    }

    protected function authorizeView(LeaveRequest $leaveRequest): void
    {
        $this->authorizeOwnership($leaveRequest);
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
