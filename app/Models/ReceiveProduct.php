<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReceiveProduct extends Model
{
    protected $fillable = [
        "user_id",
        "supplier_id",
        "status_id",
        "date_received",
        "total_price",
        "comment",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->with('roles');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function receiveProductDetails(): HasMany
    {
        return $this->hasMany(ReceiveProductDetail::class, 'receive_product_id')
            ->with(['product', 'amountType']);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
