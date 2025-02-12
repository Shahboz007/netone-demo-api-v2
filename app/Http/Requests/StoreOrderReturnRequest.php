<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderReturnRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'customer_id' => 'required|integer|exists:customers,id',
            'product_list' => 'required|array',
            'product_list.*.product_id' => 'required|integer|exists:products,id',
            'product_list.*.polka_id' => 'required|integer|exists:product_stocks,id',
            'product_list.*.amount' => 'required|numeric|min:0.01',
            'product_list.*.amount_type_id' => 'required|integer|exists:amount_types,id',
            'comment' => 'nullable|string'
        ];
    }
}
