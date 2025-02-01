<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentWalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'currency' => CurrencyResource::make($this->currency),
            'wallet' => WalletResource::make($this),
            'amount' => (float)$this->amount,
            'res_amount' => (float)$this->res_amount,
        ];
    }
}
