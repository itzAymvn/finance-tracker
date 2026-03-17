<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paid_at' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
            'allocation_mode' => ['required', 'in:auto,manual'],
            'allocations' => ['exclude_unless:allocation_mode,manual', 'array'],
            'allocations.*.salary_month_id' => ['exclude_unless:allocation_mode,manual', 'required', 'exists:salary_months,id'],
            'allocations.*.amount' => ['exclude_unless:allocation_mode,manual', 'required', 'numeric', 'min:0.01'],
        ];
    }
}
