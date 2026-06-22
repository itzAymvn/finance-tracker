<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_salary'   => ['required', 'boolean'],
            'label'       => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ];
    }

    public function prepareForValidation(): void
    {
        // Normalize unchecked checkbox.
        if (! $this->has('is_salary')) {
            $this->merge(['is_salary' => false]);
        }
    }
}
