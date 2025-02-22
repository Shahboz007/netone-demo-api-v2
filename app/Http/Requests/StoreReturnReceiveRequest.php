<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnReceiveRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'date_received' => 'required|string|max:200',
            'comment' => 'nullable|string',
            'product_list' => 'required|array',
            'product_list.*.product_id' => 'required|integer|exists:products,id',
            'product_list.*.polka_id' => 'required|integer|exists:product_stocks,id',
            'product_list.*.amount' => 'required|numeric|min:0.01',
            'product_list.*.price' => 'required|numeric|min:0',
        ];
    }
}
