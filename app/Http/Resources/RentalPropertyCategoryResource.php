<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalPropertyCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_income' => (bool) $this->is_income,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
