<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiveProductShowResource extends JsonResource
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
            "user" => UserResource::make($this->user),
            "supplier" => SupplierResource::make($this->supplier),
            "receive_product_details" => ReceiveProductDetailsResource::collection($this->receiveProductDetails),
            "total_price" => (float)$this->total_price,
            "date_received" => $this->date_received,
            "comment" => $this->comment,
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
