<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckStockRecipeItemsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            'amount' => (float) $this->amount,
            'amount_type' => $this->amountType,
            'coefficient' => (float) $this->coefficient,
            'sufficiency' => $this->product->stock->amount / $this->coefficient,
            "product" => ProductResource::make($this->product),
            "stock" => [
                "id" => $this->product->stock->id,
                "name" => $this->product->stock->name,
                "amount" => (float) $this->product->stock->amount,
                "amount_type" => $this->product->stock->amountType,
                "updated_at" => $this->product->stock->updated_at,
                "created_at" => $this->product->stock->created_at,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
