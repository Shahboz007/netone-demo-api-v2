<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderCompletedRequest extends FormRequest
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
            'product_list' => 'required|array',
            'product_list.*.product_id' => 'required|integer|exists:products,id',
            'product_list.*.completed_amount' => 'required|numeric|min:0',
            'comment' => 'nullable|string|min:6|max:255',
        ];
    }
}
