<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'customer_id' => 'required|exists:customers,id',
            'product_list' => 'required|array',
            'product_list.*.product_id' => 'required|exists:products,id',
            'product_list.*.amount_type_id' => 'required|exists:amount_types,id',
            'product_list.*.amount' => 'required|numeric|min:0',
        ];
    }
}
