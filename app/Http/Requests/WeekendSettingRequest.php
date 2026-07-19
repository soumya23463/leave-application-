<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeekendSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return isAdmin();
    }

    public function rules(): array
    {
        return [
            'days'           => ['required', 'array', 'min:1'],
            'days.*'         => ['in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'effective_date' => ['required', 'date'],
            'status'         => ['boolean'],
        ];
    }
}
