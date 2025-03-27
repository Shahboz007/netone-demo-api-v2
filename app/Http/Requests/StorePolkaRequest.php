<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePolkaRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:polkas,name',
            'parent_id' => 'nullable|string|max:255|exists:polkas,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => "Nomi talab qilinadi",
            "name.unique" => "Bu nomli polka allaqachon mavjud",
            "parent_id.exists" => "Polkani noto'g'ri tanladingiz",
        ];
    }
}
