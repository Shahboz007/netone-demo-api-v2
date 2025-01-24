<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderShowResource extends JsonResource
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
            "customer" => CustomerResource::make($this->customer),
            "order_details" => OrderDetailResource::collection($this->orderDetails),
            "total_sale_price" => (float) $this->total_sale_price,
            "total_cost_price" => auth()->user()->isAdmin() ? (float) $this->total_cost_price : 0,
            "submitted_data" => OrderSubmittedResource::make($this->submittedOrder),
            "status" => $this->status,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
        ];
    }
}
