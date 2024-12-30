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
            "order" => [
                "id" => $this->order->id,
                "customer" => CustomerResource::make($this->order->customer),
                "product" => ProductResource::make($this->order->product),
                "amount_type" => $this->order->amountType,
                "amount" => (float) $this->order->amount,
                "status" => $this->order->status,
                "updated_at" => $this->order->updated_at,
                "created_at" => $this->order->created_at,
            ],
        ];
    }
}
