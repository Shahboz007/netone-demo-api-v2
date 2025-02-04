<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentSupplierRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'supplier_id' => 'required|numeric|exists:suppliers,id',
            'wallet_list' => 'required|array|min:1',
            'wallet_list.*.wallet_id' => [
                'required',
                'numeric',
                'exists:user_wallet,wallet_id,user_id,' . auth()->id()
            ],
            'wallet_list.*.amount' => 'required|numeric|min:0.01',
            'wallet_list.*.rate_amount' => 'required|numeric|min:0.01',
            'comment' => 'nullable|string|min:6|max:255',
        ];
    }
}
