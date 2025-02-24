<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnReceiveDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float)$this->amount,
            'amount_type' => $this->amountType,
            'sale_price' => (float)$this->sale_price,
            'cost_price' => auth()->user()->isAdmin() ? (float)$this->cost_price : 0,
            'sum_sale_price' => (float)$this->sum_sale_price,
            'sum_cost_price' => (float)$this->sum_cost_price,
            'product' => ProductResource::make($this->product),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
