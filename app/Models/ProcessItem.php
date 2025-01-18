<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessItem extends Model
{
    //

    protected $fillable = [
        'production_process_id',
        'product_id',
        'amount_type_id',
        'amount',
    ];

    public function productionProcess(): BelongsTo
    {
        return $this->belongsTo(ProductionProcess::class, 'production_process_id');
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
