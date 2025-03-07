<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentRentalPropertyShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'price' => (float) $this->paymentable->price,
            'user' => UserResource::make($this->paymentable->user),
            'rental_property' => RentalPropertyResource::make($this->paymentable->rentalProperty),
            'customer' => CustomerResource::make($this->paymentable->customer),
            'user_wallet' => UserWalletResource::make($this->paymentable->userWallet),
            'wallet' => PaymentWalletResource::make($this->wallets[0]),
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
