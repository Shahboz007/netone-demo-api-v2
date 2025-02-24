<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
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
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => [
                'required',
                'numeric',
                Rule::unique('suppliers', 'phone')->ignore($this->supplier),
            ],
            'telegram' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('suppliers', 'telegram')->ignore($this->supplier),
            ],
        ];
    }

    public function messages()
    {
        return [
            'phone.unique' => 'Bu telefon raqam allaqachon mavjud!',
            'telegram.unique' => 'Bu telegram ID allaqachon mavjud!'
        ];
    }
}
