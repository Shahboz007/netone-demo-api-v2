<?php


namespace App\Services\Production;

use App\Models\ProductionRecipe;
use Illuminate\Support\Facades\DB;

class CheckStockService
{
    public function getAllByRecipe(int $id)
    {
        $stock = ProductionRecipe::with('outProduct', 'outAmountType', 'recipeItems')->findOrFail($id);

        return $stock;
    }
}
