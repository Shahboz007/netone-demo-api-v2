<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionRecipeRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'out_product_id' => 'required|integer|exists:products,id',
            'out_amount' => 'required|numeric|min:0.01',
            'items_list' => 'required|array',
            'items_list.*.raw_material_id' => 'required|integer|exists:raw_materials,id',
            'items_list.*.amount' => 'required|numeric|min:0.01',
        ];
    }
}
