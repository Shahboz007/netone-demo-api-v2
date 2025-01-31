<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserControlRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'login' => 'nullable|string|max:255|unique:users',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255|unique:users',
            'roles' => 'nullable|array',
            'roles.*' => 'required|exists:roles,id',
        ];
    }
}
