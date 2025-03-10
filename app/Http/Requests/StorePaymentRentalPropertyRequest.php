<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRentalPropertyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'rental_property_id' => 'required|exists:rental_properties,id',
            'user_wallet_id' => 'required|exists:user_wallet,id',
            'amount' => 'required|numeric|min:0.01',
            'rate_amount' => 'required|numeric|min:0.01',
            'comment' => 'nullable|string'
        ];
    }
}
