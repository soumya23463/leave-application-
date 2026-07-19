<?php

namespace App\Http\Requests;

use App\Models\LeaveBalance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class LeaveBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return isAdmin();
    }

    public function rules(): array
    {
        return [
            'employee_id'     => ['required', 'exists:users,id'],
            'total_days'      => ['required', 'numeric', 'min:0'],
            'carried_forward' => ['required', 'numeric', 'min:0'],
            'used_days'       => ['required', 'numeric', 'min:0'],
            'year'            => ['required', 'integer', 'min:2000', 'max:2100'],
        ];
    }

    /**
     * Enforce the unique(employee_id, year) constraint gracefully.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $currentId = $this->route('leave_balance')?->id;

            $exists = LeaveBalance::where('employee_id', $this->employee_id)
                ->where('year', $this->year)
                ->when($currentId, fn ($q) => $q->where('id', '!=', $currentId))
                ->exists();

            if ($exists) {
                $validator->errors()->add('year', 'A leave balance for this employee and year already exists.');
            }
        });
    }
}
