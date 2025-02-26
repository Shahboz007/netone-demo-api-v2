<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentSetMoneyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user'=> UserResource::make($this->user),
            'payment_wallet_user' => UserResource::make($this->paymentable->user),
            'payment_wallet' => WalletResource::make($this->paymentable->wallet),
            'wallet' => PaymentSetMoneyWalletResource::make($this->wallets[0]),
            'status' => $this->status,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
