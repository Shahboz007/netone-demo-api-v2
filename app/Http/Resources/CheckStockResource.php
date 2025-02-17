<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckStockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "out_amount" => (float) $this->out_amount,
            "out_product" => ProductResource::make($this->outProduct),
            "out_amount_type" => $this->outAmountType,
            "recipe_items" => CheckStockRecipeItemsResource::collection($this->recipeItems),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
