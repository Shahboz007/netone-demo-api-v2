<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            "product" => ProductResource::make($this->product),
            "amount_type" => $this->amountType,
            "amount" => (float) $this->amount,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
        ];
    }
}
