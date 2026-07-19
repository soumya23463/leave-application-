<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return isAdmin();
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ];
    }
}
