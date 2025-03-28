<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinishProductionProcessRequest extends FormRequest
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
            'total_amount' => 'required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'total_amount.min' => "Ishlab chiqarilgan jami miqdor 0 dan kam bo'lmasin"
        ];
    }
}
