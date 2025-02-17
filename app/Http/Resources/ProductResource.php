<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "name" => $this->name,
            "cost_price" => auth()->user()->isAdmin() ? (float)$this->cost_price : 0,
            "sale_price" => (float)$this->sale_price,
            "receive_price" => (float)$this->receive_price,
            "price_amount_type" => $this->priceAmountType,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
        ];
    }
}
