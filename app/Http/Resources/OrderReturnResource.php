<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderReturnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->user),
            'customer' => CustomerResource::make($this->customer),
            'total_cost_price' => auth()->user()->isAdmin() ? (float) $this->total_cost_price : 0,
            'total_sale_price' => (float) $this->total_sale_price,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
