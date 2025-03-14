<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRentalPropertyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $id = $this->route('rental_property');

        return [
            'name' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('rental_properties', 'name')->ignore($id),
            ],
            'amount' => 'nullable|numeric|min:0',
            'comment' => 'nullable|string|max:255',
        ];
    }
}
