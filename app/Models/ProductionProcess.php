<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionProcess extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionProcessFactory> */
    use HasFactory;

    protected $fillable = [
        'out_product_id',
        'out_amount'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'out_product_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
