<?php


namespace App\Services\Production;

use App\Models\ProductionRecipe;

class CheckStockService
{
    public function getRecipeByStock(int $id)
    {
        $stock = ProductionRecipe::with([
            'outProduct',
            'outAmountType',
            'recipeItems.amountType',
            'recipeItems.product.stock.amountType'
        ])
            ->findOrFail($id);

        return $stock;
    }
}
