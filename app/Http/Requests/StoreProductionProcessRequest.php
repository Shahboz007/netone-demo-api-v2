<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionProcessRequest extends FormRequest
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
            'production_recipe_id' => 'required|integer|exists:production_recipes,id',
            'items_list' => 'required|array',
            'items_list.*.product_id' => 'required|integer|exists:products,id',
            'items_list.*.amount_type_id' => 'required|integer|exists:amount_types,id',
            'items_list.*.amount' => 'required|numeric|min:0.01',
        ];
    }
}
