<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRecipeRequest;
use App\Http\Requests\UpdateProductionRecipeRequest;
use App\Http\Resources\ProductionRecipeResource;
use App\Http\Resources\ProductResource;
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

        return response()->json([
            'data' => ProductionRecipeResource::collection($data)
        ]);
    }


    public function store(StoreProductionRecipeRequest $request)
    {

        DB::beginTransaction();

        try {

            $newRecipe = ProductionRecipe::create([
                "name" => $request->validated('name'),
                'out_product_id' => $request->validated('out_product_id'),
                'out_amount_type_id' => $request->validated('out_amount_type_id'),
                'out_amount' => $request->validated('out_amount')
            ]);

            $newRecipe->recipeItems()->createMany($request->validated('items_list'));

            DB::commit();
            return response()->json([
                "message" => "Ishlab chiqarish uchun retsept yaratildi",
                "data" => $newRecipe
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }


    public function show(string $id)
    {
        $productionRecipe = ProductionRecipe::with(
            'outProduct',
            'recipeItems'
        )->findOrFail($id);

        return response()->json([
            'data' => ProductionRecipeResource::make($productionRecipe)
        ]);
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
