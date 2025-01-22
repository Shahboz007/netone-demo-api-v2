<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderCancelShowResource extends JsonResource
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
            "user" => $this->user,
            "comment" => $this->comment,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
            "order" => OrderShowResource::make($this->order),
        ];
    }
}
