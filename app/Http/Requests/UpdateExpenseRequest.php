<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:expenses,id',
            'name' => [
                'nullable',
                'string',
                'min:1',
                'max:255',
                Rule::unique('expenses', 'name')->ignore($this->route('expense')),
            ],
            'amount' => 'nullable|numeric|min:0',
            'comment' => 'nullable|string|min:6|max:255',
        ];
    }
}
