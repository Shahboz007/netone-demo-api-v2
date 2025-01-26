<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionProcess extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionProcessFactory> */
    use HasFactory;

    protected $fillable = [
        'production_recipe_id',
        'status_id',
        'out_amount'
    ];

    public function productionRecipe(): BelongsTo
    {
        return $this->belongsTo(ProductionRecipe::class, 'production_recipe_id')->with('outProduct');
    }

    public function processItems(): HasMany
    {
        return $this->hasMany(ProcessItem::class, 'production_process_id')
            ->with(['product', 'amountType']);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
