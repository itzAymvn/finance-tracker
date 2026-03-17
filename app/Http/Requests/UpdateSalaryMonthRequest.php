<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalaryMonthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('salary_month')->id;

        return [
            'month_key' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/', "unique:salary_months,month_key,{$id}"],
            'expected_salary' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'month_key.regex' => 'Month key must be in YYYY-MM format (e.g. 2025-10).',
        ];
    }
}
