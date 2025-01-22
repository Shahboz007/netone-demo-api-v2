<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAmountSettingsRequest extends FormRequest
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
            'type_from_id' => 'required|numeric|exists:amount_types,id',
            'amount_from' => 'required|numeric',
            'type_to_id' => 'required|numeric|exists:amount_types,id',
            'amount_to' => 'required|numeric',
            'comment' => 'nullable|string|max:255',
        ];
    }
}
