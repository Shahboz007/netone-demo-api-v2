<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalPropertyCategoryRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => "required|string|max:255|unique:rental_property_categories,name",
            'is_income' => 'nullable|boolean'
        ];
    }
}
