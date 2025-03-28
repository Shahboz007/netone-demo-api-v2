<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionRecipeItemResource extends JsonResource
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
            'amount' => (float) $this->amount,
            'amount_type' => $this->amountType,
            'coefficient' => (float) $this->coefficient,
            'is_change' => (bool) $this->is_change,
            'product' => ProductResource::make($this->product),
            "stock" => ProductionRecipeItemStockResource::make($this->product->stock),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
