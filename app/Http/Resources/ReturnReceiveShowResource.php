<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnReceiveShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->user),
            'supplier' => SupplierResource::make($this->supplier),
            'supplier_old_balance' => (float)$this->old_balance,
            'date_received' => $this->date_received,
            'total_sale_price' => (float)$this->total_sale_price,
            'total_cost_price' => auth()->user()->isAdmin() ? $this->total_cost_price : 0,
            'details' => ReturnReceiveDetailResource::collection($this->returnReceiveDetails),
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
