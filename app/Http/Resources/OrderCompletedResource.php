<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderCompletedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $oldBalance = (float)$this->customer_old_balance;
        $oldBalance = min($oldBalance, 0);

        $totalSalePrice = (float) $this->total_sale_price;

        return [
            'id' => $this->id,
            'total_sale_price' => $totalSalePrice,
            'total_const_price' => auth()->user()->isAdmin() ? (float)$this->total_cost_price : 0,
            'customer_old_balance' => $oldBalance,
            'current_debt' => -$totalSalePrice + $oldBalance,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
