<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paid_at'        => ['required', 'date'],
            'label'          => ['required', 'string', 'max:255'],
            'amount'         => ['required', 'numeric', 'not_in:0'],
            'amount_sign'    => ['required', 'in:1,-1'],
            'is_salary'      => ['sometimes', 'boolean'],
            'value_date'     => ['nullable', 'date'],
        ];
    }

    public function prepareForValidation(): void
    {
        // Apply sign: debit becomes negative, credit stays positive.
        $sign = (int) ($this->input('amount_sign') ?? 1);
        $amount = (float) $this->input('amount');
        if ($amount > 0) {
            $this->merge(['amount' => round($amount * $sign, 2)]);
        }

        // Debits cannot be salary.
        if ($sign < 0) {
            $this->merge(['is_salary' => false]);
        } else if (! $this->has('is_salary')) {
            $this->merge(['is_salary' => false]);
        }

        if ($this->has('value_date') && $this->input('value_date') === '') {
            $this->merge(['value_date' => null]);
        }
    }
}
