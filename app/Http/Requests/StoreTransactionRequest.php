<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'value_date'     => ['nullable', 'date'],
            'category_id'    => ['nullable', 'exists:categories,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $categoryId = $this->input('category_id');
            $sign = (int) $this->input('amount_sign');

            if ($categoryId && $sign === -1) {
                $category = Category::find($categoryId);
                if ($category?->is_salary) {
                    $validator->errors()->add('category_id', 'The salary category can only be used for credit transactions.');
                }
            }
        });
    }

    public function prepareForValidation(): void
    {
        // Apply sign: debit becomes negative, credit stays positive.
        $sign = (int) ($this->input('amount_sign') ?? 1);
        $amount = (float) $this->input('amount');
        if ($amount > 0) {
            $this->merge(['amount' => round($amount * $sign, 2)]);
        }

        if ($this->has('value_date') && $this->input('value_date') === '') {
            $this->merge(['value_date' => null]);
        }
    }
}
