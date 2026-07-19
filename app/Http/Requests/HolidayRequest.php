<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'date'        => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'status'      => ['boolean'],
        ];
    }
}
