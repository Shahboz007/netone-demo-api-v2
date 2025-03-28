<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:customers,phone',
            'telegram' => 'nullable|string|max:255|unique:customers,telegram',
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
