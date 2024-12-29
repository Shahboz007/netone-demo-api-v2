<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductStockRequest extends FormRequest
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
            'product_id' => 'required|numeric|exists:products,id',
            'name' => 'required|string|unique:stocks,name',
            'amount_type_id' => 'required|numeric|exists:amount_types,id',
            'amount' => 'required|numeric|min:0'
        ];
    }
}
