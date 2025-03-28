<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'user_id' => 'nullable|numeric|exists:users,id',
            // 'name' => 'nullable|string|unique:departs,name',
            'name' => [
                'nullable',
                'string',
                Rule::unique('departs', 'name')->ignore($this->department),
            ],
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
