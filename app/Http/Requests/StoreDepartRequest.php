<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'user_id' => 'nullable|numeric|exists:users,id',
            'name' => 'required|string|unique:departs,name',
            'comments' => 'nullable|string|min:6|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => "Bu bo'lim allaqachon mavjud"
        ];
    }
}
