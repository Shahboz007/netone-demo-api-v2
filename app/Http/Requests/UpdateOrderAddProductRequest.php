<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderAddProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'amount_type_id' => 'required|exists:amount_types,id',
            'amount' => 'required|numeric|min:0',
        ];
    }
}
