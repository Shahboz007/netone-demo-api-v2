<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentSetMoneyWalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'wallet' => WalletResource::make($this),
            'amount' => (float) $this->pivot->amount,
            'rate_amount' => (float) $this->pivot->rate_amount,
            'sum_price' => (float) $this->pivot->sum_price,
        ];
    }
}
