<?php

namespace App\Http\Requests;

use App\Models\ProductStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProductStockRequest extends FormRequest
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
            'product_id' => 'required|numeric|exists:products,id',
            'polka_id' => 'required|numeric|exists:polkas,id',
            'amount_type_id' => 'required|numeric|exists:amount_types,id',
            'amount' => 'required|numeric|min:0'
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $product_id = $this->input('product_id');
            $polka_id = $this->input('polka_id');

            if (ProductStock::where('product_id', $product_id)
                ->where('polka_id', $polka_id)
                ->exists()
            ) {
                $validator->errors()->add('product_id', 'Bu product va polka juftligi allaqachon mavjud.');
            }
        });
    }
}
