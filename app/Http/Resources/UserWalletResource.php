<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWalletResource extends JsonResource
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
            'wallet' => WalletResource::make($this),
            'currency' => CurrencyResource::make($this->currency),
            'amount' => (float) $this->pivot->amount,
        ];
    }
}
