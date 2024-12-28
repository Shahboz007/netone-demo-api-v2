<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterial extends Model
{
    /** @use HasFactory<\Database\Factories\RawMaterialFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'amount_type_id',
        'amount'
    ];

    public function amountType(): BelongsTo
    {
        return $this->belongsTo(AmountType::class, 'amount_type_id');
    }
}
