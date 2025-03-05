<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRecipeRequest;
use App\Http\Requests\UpdateProductionRecipeRequest;
use App\Http\Resources\ProductionRecipeResource;
use App\Models\ProductionRecipe;
use App\Services\AmountConverterService;
use Illuminate\Support\Facades\DB;

class ProductionRecipeController extends Controller
{
    public function __construct(
        protected AmountConverterService $amountConverter
    ) {}

    public function index()
    {
        $data = ProductionRecipe::with(
            'outProduct',
            'recipeItems.product'
        )->get();

        return response()->json([
            'data' => ProductionRecipeResource::collection($data)
        ]);
    }


    public function store(StoreProductionRecipeRequest $request)
    {

        DB::beginTransaction();

        try {

            // New Recipe
            $newRecipe = ProductionRecipe::create([
                "name" => $request->validated('name'),
                'out_product_id' => $request->validated('out_product_id'),
                'out_amount_type_id' => $request->validated('out_amount_type_id'),
                'out_amount' => $request->validated('out_amount')
            ]);


            // Create Recipe Items
            $list = [];
            foreach ($request->validated('items_list') as $item) {
                $list[] = [
                    'production_recipe_id' => $newRecipe->id,
                    "product_id" => $item['product_id'],
                    "amount" => $item["amount"],
                    "amount_type_id" => $item['amount_type_id'],
                    'coefficient' => $item['amount'] / $request->validated('out_amount'),
                    'is_change' => $request->validated('is_change'),
                ];
            }
            $newRecipe->recipeItems()->createMany($list);

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
