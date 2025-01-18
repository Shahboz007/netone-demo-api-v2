<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionProcessResource extends JsonResource
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
            'production_recipe' => [
                'id' => $this->productionRecipe->id,
                'name' => $this->productionRecipe->name,
                'out_amount' => (float) $this->productionRecipe->out_amount,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'process_items' => ProductionProcessItemResource::collection($this->processItems),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
