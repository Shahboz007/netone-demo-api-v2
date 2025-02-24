<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentGetMoneyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }


    public function rules(): array
    {
        return [
            'get_money_id' => 'required|exists:get_money,id',
            'user_wallet_id' => [
                'required',
                'integer',
                'exists:user_wallet,wallet_id',
            ],
            'amount' => 'required|numeric|min:0',
            'rate_amount' => 'required|numeric|min:1',
            'comment' => 'nullable|string|min:3|max:255',
        ];
    }
}
