<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalPropertyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:rental_properties,name',
            'price' => 'required|numeric|min:0',
            'comment' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Bu tijorat oyekti allaqachon mavjud'
        ];
    }
}
