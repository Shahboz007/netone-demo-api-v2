<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessItem extends Model
{
    //

    protected $fillable = [
        'production_process_id',
        'raw_material_id',
        'amount'
    ];

    public function productionProcess(): BelongsTo
    {
        return $this->belongsTo(ProductionProcess::class, 'production_process_id');
    }

    // public function rawMaterial(): BelongsTo
    // {
    //     return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    // }
}
