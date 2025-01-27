<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiveProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user" => $this->user,
            "supplier" => $this->supplier,
            "product" => ProductResource::make($this->product),
            "amount_type" => $this->amountType,
            "amount" => (float) $this->amount,
            "received_date" => $this->received_date,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
