<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRawMaterialRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255|unique:raw_materials,name',
            'amount_type_id' => 'required|numeric|exists:amount_types,id',
            'amount' => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            "name.unique" => "Bu mahsulot allaqachon mavjud!",
        ];
    }
}
