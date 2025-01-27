<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    //

    protected $fillable = [
        'order_id',
        'product_id',
        'amount_type_id',
        'completed_amount',
        'amount',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function amountType(): BelongsTo
    {
        return $this->belongsTo(AmountType::class, 'amount_type_id');
    }
}
