<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentExpenseResource extends JsonResource
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
            'user' => UserResource::make($this->user),
            'expense' => ExpenseResource::make($this->paymentable),
            'wallet' => PaymentWalletResource::make($this->wallets[0]),
            'total_price' => (float)$this->wallets[0]->pivot->sum_price,
            'status' => $this->status,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
