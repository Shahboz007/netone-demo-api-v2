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
            "user" => UserResource::make($this->user),
            "supplier" => SupplierResource::make($this->supplier),
            "product" => ProductResource::make($this->product),
            "amount_type" => $this->amountType,
            "amount" => (float) $this->amount,
            "price" => (float) $this->price,
            "total_price" => (float) $this->total_price,
            "received_date" => $this->received_date,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
