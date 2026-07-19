<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'from_date' => ['required', 'date', 'after_or_equal:today'],
            'to_date'   => ['required', 'date', 'after_or_equal:from_date'],
            'reason'    => ['required', 'string', 'max:2000'],
        ];

        // Admins choose the employee; employees always file for themselves.
        if (isAdmin()) {
            $rules['user_id'] = ['required', 'exists:users,id'];
        }

        return $rules;
    }
}
