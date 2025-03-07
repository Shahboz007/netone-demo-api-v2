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
            'status' => $this->status,
            'total_price' => (float) $this->paymentable->total_price,
            'user' => UserResource::make($this->paymentable->user),
            'rental_property' => RentalPropertyResource::make($this->paymentable->rentalProperty),
            'customer' => CustomerResource::make($this->paymentable->customer),
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
