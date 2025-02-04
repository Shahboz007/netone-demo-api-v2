<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'sale_price' => 'required|numeric|min:0',
            'price_amount_type_id' => 'required|integer|exists:amount_types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Bu mahsulot nomi allaqachon mavjud',
        ];
    }
}
