<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AmountSettings extends Pivot
{
    protected $fillable = [
        'type_from_id',
        'amount_from',
        'type_to_id',
        'amount_to',
        'comment'
    ];

    public function typeFrom(): BelongsTo
    {
        return $this->belongsTo(AmountType::class, 'type_from_id');
    }

    public function typeTo(): BelongsTo
    {
        return $this->belongsTo(AmountType::class, 'type_to_id');
    }
}
