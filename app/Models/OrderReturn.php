<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'comment',
        'total_sale_price',
        'total_cost_price',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'order_id');
    }

    public function order():BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
