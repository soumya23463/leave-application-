<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeaveBalanceRequest;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveBalanceController extends Controller
{
    /**
     * This resource manages yearly leave balances for employees.
     */
    public function index(Request $request)
    {
        $query = LeaveBalance::with('employee')->orderByDesc('year');

        if (! isAdmin()) {
            // Employees see only their own balance.
            $query->where('employee_id', authId());
        } elseif (! isSuperAdmin()) {
            // Regular admins see only their own department.
            $deptId = authUser()->department_id;
            $query->whereHas('employee', fn ($q) => $q->where('department_id', $deptId));
        }

        if (isAdmin() && $request->filled('year')) {
            $query->where('year', $request->year);
        }

        $leaveBalances = $query->paginate(15)->withQueryString();

        $years = LeaveBalance::distinct()->orderByDesc('year')->pluck('year');

        return view('leave-balances.index', compact('leaveBalances', 'years'));
    }

    public function create()
    {
        abort_unless(isAdmin(), 403);

        $employees = $this->employees();

        return view('leave-balances.create', compact('employees'));
    }

    public function store(LeaveBalanceRequest $request)
    {
        $data = $request->validated();

        $this->assertEmployeeInDepartment($data['employee_id'] ?? null);

        $data['remaining_days'] = $data['total_days'] + $data['carried_forward'] - $data['used_days'];

        LeaveBalance::create($data);

        return redirect()->route('leave-balances.index')->with('success', 'Leave balance created.');
    }

    public function show(LeaveBalance $leaveBalance)
    {
        abort_if(! isAdmin() && $leaveBalance->employee_id !== authId(), 403);
        $this->ensureSameDepartment($leaveBalance);

        $leaveBalance->load('employee');

        return view('leave-balances.show', compact('leaveBalance'));
    }

    public function edit(LeaveBalance $leaveBalance)
    {
        abort_unless(isAdmin(), 403);
        $this->ensureSameDepartment($leaveBalance);

        $employees = $this->employees();

        return view('leave-balances.edit', compact('leaveBalance', 'employees'));
    }

    public function update(LeaveBalanceRequest $request, LeaveBalance $leaveBalance)
    {
        $this->ensureSameDepartment($leaveBalance);

        $data = $request->validated();

        $this->assertEmployeeInDepartment($data['employee_id'] ?? $leaveBalance->employee_id);

        $data['remaining_days'] = $data['total_days'] + $data['carried_forward'] - $data['used_days'];

        $leaveBalance->update($data);

        return redirect()->route('leave-balances.index')->with('success', 'Leave balance updated.');
    }

    public function destroy(LeaveBalance $leaveBalance)
    {
        abort_unless(isAdmin(), 403);
        $this->ensureSameDepartment($leaveBalance);

        $leaveBalance->delete();

        return redirect()->route('leave-balances.index')->with('success', 'Leave balance deleted.');
    }

    // ---- helpers ----

    protected function employees()
    {
        return User::where('role', 'employee')
            ->when(! isSuperAdmin(), fn ($q) => $q->where('department_id', authUser()->department_id))
            ->orderBy('name')->get();
    }

    /**
     * Regular admins may only touch balances of employees in their own department.
     */
    protected function ensureSameDepartment(LeaveBalance $leaveBalance): void
    {
        if (isAdmin() && ! isSuperAdmin()
            && $leaveBalance->employee?->department_id !== authUser()->department_id) {
            abort(403, 'You can only manage balances for your own department.');
        }
    }

    protected function assertEmployeeInDepartment(?int $employeeId): void
    {
        if (! isAdmin() || isSuperAdmin() || ! $employeeId) {
            return;
        }

        $target = User::find($employeeId);
        if (! $target || $target->department_id !== authUser()->department_id) {
            abort(403, 'You can only manage balances for your own department.');
        }
    }
}
