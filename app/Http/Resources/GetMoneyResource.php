<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetMoneyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "amount" => (float) $this->amount,
            "comment" => $this->comment,
            "parent_id" => $this->parent_id,
            "children" => self::collection($this->children),
        ];
    }
}
