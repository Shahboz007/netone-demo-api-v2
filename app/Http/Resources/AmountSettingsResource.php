<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmountSettingsResource extends JsonResource
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
            'type_from' => $this->typeFrom,
            'amount_from' => $this->amount_from,
            'type_to' => $this->typeTo,
            'amount_to' => $this->amount_to,
            'created_at' => $this->created_at,
            'update_at' => $this->updated_at,
        ];
    }
}
