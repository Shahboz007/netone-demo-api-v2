<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentCustomerRequest extends FormRequest
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
            'customer_id' => 'required|numeric|exists:customers,id',
            'wallet_list' => 'required|array|min:1',
            'wallet_list.*.wallet_id' => 'required|numeric|exists:wallets,id',
            'wallet_list.*.amount' => 'required|numeric|min:0',
            'wallet_list.*.rate_amount' => 'required|numeric|min:0',
            'comment' => 'nullable|string|min:6|max:255',
        ];
    }
}
