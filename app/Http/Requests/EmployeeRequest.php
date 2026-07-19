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
        $employeeId = $this->route('employee')?->id;

        return [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($employeeId)],
            'password'     => [$employeeId ? 'nullable' : 'required', 'min:6'],
            'phone'        => ['nullable', 'string', 'max:255'],
            'role'         => ['required', 'in:admin,employee'],
            'status'       => ['required', 'in:active,inactive'],
            'joining_date' => ['nullable', 'date'],
        ];
    }
}
