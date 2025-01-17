<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRecipeRequest;
use App\Http\Requests\UpdateProductionRecipeRequest;
use App\Models\ProductionRecipe;
use Illuminate\Support\Facades\DB;

class ProductionRecipeController extends Controller
{
    public function index()
    {
        $data = ProductionRecipe::with(
            'outProduct',
            'recipeItems'
        )->get();

        return $data;
    }


    public function store(StoreProductionRecipeRequest $request)
    {

        DB::beginTransaction();

        try {

            $newRecipe = ProductionRecipe::create([
                "name" => $request->validated('name'),
                'out_product_id' => $request->validated('out_product_id'),
                'out_amount' => $request->validated('out_amount')
            ]);

            $newRecipe->recipeItems()->createMany($request->validated('items_list'));

            DB::commit();
            return response()->json([
                "message" => "Ishlab chiqarish uchun retsept yaratildi",
                "data" => $newRecipe
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError();
        }
    }


    public function show(ProductionRecipe $productionRecipe)
    {
        //
    }


    public function update(UpdateProductionRecipeRequest $request, ProductionRecipe $productionRecipe)
    {
        //
    }


    public function destroy(ProductionRecipe $productionRecipe)
    {
        //
    }
}
