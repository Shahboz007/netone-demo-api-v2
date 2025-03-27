<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ProductStock extends Model
{
    /** @use HasFactory<\Database\Factories\ProductStockFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'polka_id',
        'amount_type_id',
        'amount',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function polka(): BelongsTo
    {
        return $this->belongsTo(Polka::class, foreignKey: 'polka_id');
    }

    public function amountType(): BelongsTo
    {
        return $this->belongsTo(AmountType::class, 'amount_type_id');
    }
}
