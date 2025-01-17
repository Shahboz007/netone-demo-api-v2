<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionRecipe extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionRecipeFactory> */
    use HasFactory;

    protected $fillable = [
        'out_product_id',
        'name',
        'out_amount',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'out_product_id');
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItems::class, 'production_recipe_id');
    }
}
