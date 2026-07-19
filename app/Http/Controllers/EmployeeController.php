<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * This resource manages ALL users (both admin and employee roles).
     */
    public function index(Request $request)
    {
        $query = User::orderBy('name');

        if (in_array($request->status, ['active', 'inactive'], true)) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('employees.index', compact('users'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(EmployeeRequest $request)
    {
        $user = User::create($request->validated());

        LeaveBalance::create([
            'employee_id'     => $user->id,
            'total_days'      => 24,
            'used_days'       => 0,
            'remaining_days'  => 24,
            'carried_forward' => 0,
            'year'            => now()->year,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee created.');
    }

    public function show(User $employee)
    {
        $leaveBalances = $employee->leaveBalances()->orderByDesc('year')->get();

        return view('employees.show', [
            'user'          => $employee,
            'leaveBalances' => $leaveBalances,
        ]);
    }

    public function edit(User $employee)
    {
        return view('employees.edit', ['user' => $employee]);
    }

    public function update(EmployeeRequest $request, User $employee)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated.');
    }

    public function destroy(User $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted.');
    }
}
