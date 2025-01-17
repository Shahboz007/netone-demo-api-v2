<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeItems extends Model
{
    //

    protected $fillable = [
        'production_recipe_id',
        'raw_material_id',
        'amount',
    ];

    public function productionRecipe(): BelongsTo
    {
        return $this->belongsTo(ProductionRecipe::class, 'production_recipe_id');
    }

    // public function rawMaterial(): BelongsTo
    // {
    //     return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    // }
}
