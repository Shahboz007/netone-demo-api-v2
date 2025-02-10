<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderReturnRequest extends FormRequest
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
            'order_id' => 'required|exists:orders,id',
            'order_item_list' => 'required|array',
            'order_item_list.*.item_id' => 'required|integer|exists:order_details,id',
            'order_item_list.*.amount' => 'required|numeric|min:0.01',
            'order_item_list.*.amount_type_id' => 'required|exists:amount_types,id',
            'comment' => 'nullable|string'
        ];
    }
}
