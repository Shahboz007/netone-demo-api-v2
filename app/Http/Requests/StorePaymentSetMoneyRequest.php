<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentSetMoneyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'user_wallet_id' => 'required|integer|exists:user_wallet,id',
            'amount' => 'required|numeric|min:1',
            'rate_amount' => 'required|numeric|min:1',
            'comment' => 'nullable|string|min:6|max:255',
        ];
    }
}
