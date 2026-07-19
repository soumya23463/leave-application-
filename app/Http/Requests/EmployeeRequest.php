<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return isAdmin();
    }

    public function rules(): array
    {
        $editing    = $this->route('employee');
        $employeeId = $editing?->id;

        // Only super admins may assign the superadmin role. Keep the target's
        // existing role valid so editing a superadmin doesn't get rejected.
        $allowedRoles = isSuperAdmin() ? ['superadmin', 'admin', 'employee'] : ['admin', 'employee'];
        if ($editing && ! in_array($editing->role, $allowedRoles, true)) {
            $allowedRoles[] = $editing->role;
        }

        return [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($employeeId)],
            'password'     => [$employeeId ? 'nullable' : 'required', 'min:6'],
            'phone'        => ['nullable', 'string', 'max:255'],
            'role'          => ['required', Rule::in($allowedRoles)],
            'status'        => ['required', 'in:active,inactive'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'joining_date'  => ['nullable', 'date'],
        ];
    }
}
