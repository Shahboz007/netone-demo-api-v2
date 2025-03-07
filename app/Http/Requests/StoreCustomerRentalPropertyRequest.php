<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRentalPropertyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'rental_property_id' => 'required|integer|exists:rental_properties,id',
            'customer_id' => 'required|integer|exists:customers,id',
            'price' => 'required|numeric|min:1',
            'comment' => 'nullable|string|min:6|max:255'
        ];
    }
}
