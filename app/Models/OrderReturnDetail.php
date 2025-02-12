<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturnDetail extends Model
{
    //

    protected $fillable = [
        "order_return_id",
        "product_id",
        "polka_id",
        "amount_type_id",
        "amount",
        "cost_price",
        "sale_price",
        "sum_cost_price",
        "sum_sale_price",
    ];

    public function orderReturn()
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productStock(): BelongsTo
    {
        return $this->belongsTo(ProductStock::class, 'polka_id');
    }

    public function amountType(): BelongsTo
    {
        return $this->belongsTo(AmountType::class, 'amount_type_id');
    }
}
