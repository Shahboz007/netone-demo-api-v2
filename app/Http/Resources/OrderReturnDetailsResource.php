<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderReturnDetailsResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount_type' => $this->amountType,
            'amount' => (float)$this->amount,
            'cost_price' => (float) $this->cost_price,
            'sale_price' => (float) $this->sale_price,
            'sum_sale_price' => (float) $this->sum_sale_price,
            'sum_cost_price' => (float) $this->sum_cost_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
