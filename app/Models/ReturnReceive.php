<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnReceive extends Model
{
    /** @use HasFactory<\Database\Factories\ReturnReceiveFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supplier_id',
        'date_received',
        'total_sale_price',
        'total_cost_price',
        'comment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function returnReceiveDetails(): HasMany
    {
        return $this->hasMany(ReturnReceiveDetail::class, 'return_receive_id');
    }
}
