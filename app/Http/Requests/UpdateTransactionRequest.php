<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'amount' => ['sometimes', 'numeric', 'gt:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $transaction = $this->route('transaction');

            if ($transaction?->isSalary() && $this->has('amount')) {
                $validator->errors()->add('amount', 'Cannot edit the amount of a salary transaction.');
            }

            $categoryId = $this->input('category_id');

            if ($categoryId) {
                $category = Category::find($categoryId);
                if ($category?->is_salary) {
                    if ($transaction && (float) $transaction->amount < 0) {
                        $validator->errors()->add('category_id', 'The salary category can only be used for credit transactions.');
                    }
                }
            }
        });
    }
}
