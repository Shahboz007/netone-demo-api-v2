<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            'ord_code' => $this->ord_code,
            "user" => $this->user,
            "customer" => CustomerResource::make($this->customer),
            "total_cost_price" => auth()->user()->isAdmin() ? (float) $this->total_cost_price : 0,
            "total_sale_price" => (float) $this->total_sale_price,
            "completed_data" => OrderCompletedResource::make($this->completedOrder),
            "status" => $this->status,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
        ];
    }
}
