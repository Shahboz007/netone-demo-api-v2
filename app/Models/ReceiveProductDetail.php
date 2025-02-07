<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiveProductDetail extends Model
{
    protected $fillable = [
        'receive_product_id',
        'product_id',
        'amount_type_id',
        'status_id',
        'amount',
        'price',
        'sum_price',
    ];

    public function receiveProduct(): BelongsTo
    {
        return $this->belongsTo(ReceiveProduct::class, 'receive_product_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function amountType(): BelongsTo
    {
        return $this->belongsTo(AmountType::class, 'amount_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
