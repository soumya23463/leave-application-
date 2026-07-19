<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return isAdmin();
    }

    public function rules(): array
    {
        return [
            'user_id'   => ['required', 'exists:users,id'],
            'from_date' => ['required', 'date', 'after_or_equal:today'],
            'to_date'   => ['required', 'date', 'after_or_equal:from_date'],
            'reason'    => ['required', 'string', 'max:2000'],
        ];
    }
}
