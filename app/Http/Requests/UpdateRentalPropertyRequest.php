<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalPropertyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return false;
    }


    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'comment' => 'nullable|string|max:255',
        ];
    }
}
