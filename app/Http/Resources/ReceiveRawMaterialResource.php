<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiveRawMaterialResource extends JsonResource
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
            "date_received" => $this->date_received,
            "raw_material" => RawMaterialResource::make($this->rawMaterial),
            "amount_type" => $this->amountType,
            "amount" => (float) $this->amount
        ];
    }
}
