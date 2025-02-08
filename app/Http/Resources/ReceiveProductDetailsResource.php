<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiveProductDetailsResource extends JsonResource
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
            'product' => ProductResource::make($this->product),
            'amount_type' => $this->amountType,
            'amount' => (float)$this->amount,
            'price' => (float)$this->price,
            'sum_price' => (float)$this->sum_price,
        ];
    }
}
