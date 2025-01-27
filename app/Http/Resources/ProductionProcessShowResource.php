<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionProcessShowResource extends JsonResource
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
            'production_recipe' => ProductionRecipeResource::make($this->productionRecipe),
            'process_items' => ProductionProcessItemResource::collection($this->processItems),
            'out_amount' => (float) $this->out_amount,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
