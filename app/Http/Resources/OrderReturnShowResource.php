<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderReturnShowResource extends JsonResource
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
            'user' => UserResource::make($this->user),
            'order' => OrderResource::make($this->order),
            'total_sale_price' => (float)$this->total_sale_price,
            'total_cost_price' => (float)$this->total_cost_price,
            'details_list' => OrderReturnDetailsResource::collection($this->orderReturnDetails),
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
