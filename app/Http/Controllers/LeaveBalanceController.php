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
            $query->where('employee_id', authId());
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

        $data['remaining_days'] = $data['total_days'] + $data['carried_forward'] - $data['used_days'];

        LeaveBalance::create($data);

        return redirect()->route('leave-balances.index')->with('success', 'Leave balance created.');
    }

    public function show(LeaveBalance $leaveBalance)
    {
        abort_if(! isAdmin() && $leaveBalance->employee_id !== authId(), 403);

        $leaveBalance->load('employee');

        return view('leave-balances.show', compact('leaveBalance'));
    }

    public function edit(LeaveBalance $leaveBalance)
    {
        abort_unless(isAdmin(), 403);

        $employees = $this->employees();

        return view('leave-balances.edit', compact('leaveBalance', 'employees'));
    }

    public function update(LeaveBalanceRequest $request, LeaveBalance $leaveBalance)
    {
        $data = $request->validated();

        $data['remaining_days'] = $data['total_days'] + $data['carried_forward'] - $data['used_days'];

        $leaveBalance->update($data);

        return redirect()->route('leave-balances.index')->with('success', 'Leave balance updated.');
    }

    public function destroy(LeaveBalance $leaveBalance)
    {
        abort_unless(isAdmin(), 403);

        $leaveBalance->delete();

        return redirect()->route('leave-balances.index')->with('success', 'Leave balance deleted.');
    }

    // ---- helpers ----

    protected function employees()
    {
        return User::where('role', 'employee')->orderBy('name')->get();
    }
}
