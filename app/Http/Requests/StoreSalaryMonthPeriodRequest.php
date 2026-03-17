<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryMonthPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_month' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'to_month' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/', 'gte:from_month'],
            'expected_salary' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'from_month.regex' => 'Start month must be in YYYY-MM format.',
            'to_month.regex' => 'End month must be in YYYY-MM format.',
            'to_month.gte' => 'End month must be the same as or after the start month.',
        ];
    }
}
