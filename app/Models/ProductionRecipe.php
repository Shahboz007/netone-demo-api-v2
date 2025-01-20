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
        'out_amount_type_id',
        'out_amount',
    ];

    public function outProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'out_product_id');
    }

    public function outAmountType(): BelongsTo
    {
        return $this->belongsTo(AmountType::class, 'out_amount_type_id');
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItems::class, 'production_recipe_id')->with('product');
    }
}
