<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Department;
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
        $query = User::with('department')->orderBy('name');

        // Regular admins only manage their own department.
        if (! isSuperAdmin()) {
            $query->where('department_id', authUser()->department_id);
        }

        if (in_array($request->status, ['active', 'inactive'], true)) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('employees.index', compact('users'));
    }

    public function create()
    {
        return view('employees.create', ['departments' => Department::orderBy('name')->get()]);
    }

    public function store(EmployeeRequest $request)
    {
        $data = $request->validated();

        // A regular admin can only create employees within their own department.
        if (! isSuperAdmin()) {
            if (! authUser()->department_id) {
                return back()->withInput()
                    ->with('error', 'You must be assigned to a department before creating employees.');
            }
            $data['department_id'] = authUser()->department_id;
        }

        $user = User::create($data);

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
        $this->ensureSameDepartment($employee);

        $leaveBalances = $employee->leaveBalances()->orderByDesc('year')->get();

        return view('employees.show', [
            'user'          => $employee,
            'leaveBalances' => $leaveBalances,
        ]);
    }

    public function edit(User $employee)
    {
        $this->ensureSameDepartment($employee);

        return view('employees.edit', [
            'user'        => $employee,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(EmployeeRequest $request, User $employee)
    {
        $this->ensureSameDepartment($employee);

        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // A regular admin cannot move an employee out of their own department.
        if (! isSuperAdmin()) {
            $data['department_id'] = authUser()->department_id;
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated.');
    }

    public function destroy(User $employee)
    {
        $this->ensureSameDepartment($employee);

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted.');
    }

    /**
     * Regular admins may only touch employees inside their own department.
     */
    protected function ensureSameDepartment(User $employee): void
    {
        if (! isSuperAdmin() && $employee->department_id !== authUser()->department_id) {
            abort(403, 'You can only manage employees in your own department.');
        }
    }
}
