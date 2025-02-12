<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderReturn extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'comment',
        'total_sale_price',
        'total_cost_price',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->with('roles');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id')
            ->with(['user', 'customer', 'completedOrder']);
    }

    public function orderReturnDetails(): HasMany
    {
        return $this->hasMany(OrderReturnDetail::class, 'order_return_id')
            ->with(['orderDetail.product', 'orderDetail.amountType', 'amountType']);
    }
}
