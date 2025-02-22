<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnReceiveDetail extends Model
{
    //

    protected $fillable = [
        'return_receive_id',
        'product_id',
        'amount_type_id',
        'amount',
        'sale_price',
        'cost_price',
        'sum_sale_price',
        'sum_cost_price',
    ];

    public function returnReceive(): BelongsTo
    {
        return $this->belongsTo(ReturnReceive::class, 'return_receive_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function amountTypeId(): BelongsTo
    {
        return $this->belongsTo(AmountType::class, 'amount_type_id');
    }
}
