<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRentalPropertyCategoryRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:rental_property_categories,id',
            'name' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('rental_property_categories')->ignore($this->route('rental_property_category'))
            ],
            'is_income' => 'nullable|boolean'
        ];
    }
}
