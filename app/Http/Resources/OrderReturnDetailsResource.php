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
            'product' => ProductResource::make($this->product),
            'product_stock' => [
                'id' => $this->productStock->id,
                'name' => $this->productStock->name,
                'amount_type_id' => $this->productStock->amount_type_id,
            ],
            'amount_type' => $this->amountType,
            'amount' => (float)$this->amount,
            'cost_price' => auth()->user()->isAdmin() ? (float) $this->cost_price : 0,
            'sale_price' => (float) $this->sale_price,
            'sum_sale_price' => (float) $this->sum_sale_price,
            'sum_cost_price' => auth()->user()->isAdmin() ?  (float) $this->sum_cost_price : 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
