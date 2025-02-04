<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'sale_price' => 'nullable|numeric|min:0',
            'price_amount_type_id' => 'nullable|integer|exists:amount_types,id',
        ];
    }
}
