<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentRentalPropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->user),
            'rental_property' => RentalPropertyResource::make($this->paymentable->rentalProperty),
            'rental_property_category' => $this->paymentable->rentalPropertyCategory,
            'total_amount' => (float) $this->total_amount,
            'comment' => $this->comment,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
