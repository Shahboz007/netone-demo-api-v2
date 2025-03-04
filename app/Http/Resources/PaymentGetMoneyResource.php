<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGetMoneyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user" => UserResource::make($this->user),
            "wallet_user" => UserResource::make($this->paymentable->userWallet->user),
            "get_money" => GetMoneyResource::make($this->paymentable->getMoney),
            "sum_amount" => (float) $this->paymentable->sum_amount,
            "wallet" => PaymentGetMoneyWalletResource::make($this->wallets[0]),
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
