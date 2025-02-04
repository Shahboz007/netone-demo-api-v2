<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishProductionProcessRequest;
use App\Http\Requests\StoreProductionProcessRequest;
use App\Http\Resources\ProductionProcessResource;
use App\Http\Resources\ProductionProcessShowResource;
use App\Models\Product;
use App\Models\ProductionProcess;
use App\Models\ProductStock;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductionProcessController extends Controller
{
    public function index(): JsonResponse
    {
        $data = ProductionProcess::with(
            'productionRecipe',
            'processItems',
            'status'
        )->latest()->get();

        return response()->json([
            'data' => ProductionProcessResource::collection($data),
        ]);
    }


    public function store(StoreProductionProcessRequest $request)
    {

        // Validation Stock
        $validateItems = $request->validated('items_list');
        $validateItemsKeys = array_column($validateItems, 'product_id');

        // Stock Items
        $stockItems = ProductStock::whereIn('product_id', $validateItemsKeys)->get();
        $pluckStockItems = $stockItems->pluck('amount', 'product_id');

        // Products
        $pluckProducts = Product::whereIn('id', $validateItemsKeys)->get()->pluck('name', 'id');

        if ($pluckStockItems->isEmpty()) {
            abort(422, "Zaxira mavjud emas");
        }

        foreach ($validateItems as $item) {
            $productName = $pluckProducts->get($item['product_id']);

            if (!$pluckStockItems->has($item['product_id'])) {
                abort(422, "`$productName` mahsulotning zaxirasi mavjud emas!");
            }

            if ($pluckStockItems->get($item['product_id']) < $item['amount']) {
                abort(422, "`$productName` mahsulotning zaxirasi yetarli emas!");
            }
        }

        // Pluck Request Items
//        $pluckReqProductsAmountType = collect($validateItems)->pluck('amount_type_id', 'product_id');
        $pluckReqProducts = collect($validateItems)->pluck('amount', 'product_id');

        DB::beginTransaction();

        try {
            $processStatus = Status::where('code', 'productionProcess')->firstOrFail();

            $newProcess = ProductionProcess::create([
                'status_id' => $processStatus->id,
                'production_recipe_id' => $request->validated('production_recipe_id'),
                'out_amount' => 0,
            ]);

            $newProcess->processItems()->createMany($request->validated('items_list'));

            foreach ($stockItems as $item) {
                $amount = $pluckReqProducts->get($item->product_id);

                $item->decrement('amount', $amount);
            }

            DB::commit();

            return response()->json([
                'message' => "Yangi ishlab chiqarish jarayoni qo'shildi",
                'data' => $newProcess,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }


    public function show(string $id): JsonResponse
    {
        $data = ProductionProcess::with(
            'productionRecipe.recipeItems',
            'processItems',
            'status'
        )->findOrFail($id);

        return response()->json([
            'data' => ProductionProcessShowResource::make($data)
        ]);
    }


    public function finish(FinishProductionProcessRequest $request, string $id)
    {
        $productionProcess = ProductionProcess::with('productionRecipe', 'processItems')->findOrFail($id);

        // Status productionCompleted
        $statusCurrent = Status::findOrFail($productionProcess->status_id);

        if ($statusCurrent->code !== 'productionProcess') {
            if ($statusCurrent->code === 'productionCancel') {
                abort(422, 'Bu ishlab chiqarish jarayoni allaqachon bekor qilingan');
            } else if ($statusCurrent->code === 'productionStopped') {
                abort(422, "Bu ishlab chiqarish jarayoni allaqachon to'xtatilgan");
            } else if ($statusCurrent->code === 'productionCompleted') {
                abort(422, "Bu ishlab chiqarish jarayoni allaqachon tayyorlangan");
            }

            abort(422, "Bu ishlab chiqarish jarayonini tugallab bo'lmaydi");
        }

        // Status productionCompleted
        $statusProductionCompleted = Status::where('code', 'productionCompleted')->firstOrFail();

        $stock = ProductStock::findOrFail($productionProcess->productionRecipe->out_product_id);
        $outProduct = Product::findOrFail($productionProcess->productionRecipe->out_product_id);

        DB::beginTransaction();

        try {
            $productionProcess->status_id = $statusProductionCompleted->id;
            $productionProcess->out_amount = $request->validated('total_amount');

            // Change stock
            $stock->increment('amount', $productionProcess->out_amount);

            $productionProcess->save();

            // Set cost price
            $costPrice = 0;

            foreach ($productionProcess->processItems as $item) {
                $costPrice += $item->amount * $item->product->sale_price;
            }

            $outProduct->update(['cost_price' => $costPrice]);

            $outProduct->save();

            DB::commit();

            return response()->json([
                'message' => "$id. Ishlab chiqarish jarayoni yakunlandi"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }


    public function cancel(string $id): JsonResponse
    {
        $data = ProductionProcess::with(
            'productionRecipe',
            'processItems',
            'status'
        )->findOrFail($id);

        // Status productionProcess
        $statusProductionProcess = Status::where('code', 'productionProcess')
            ->where('id', $data->status_id)
            ->exists();

        if (!$statusProductionProcess) abort(422, "Bu ishlab chiqarish jarayonini bekor qilib bo'lmaydi");

        // Status productionCancel
        $statusProductionCancel = Status::where('code', 'productionCancel')->firstOrFail();

        $data->status_id = $statusProductionCancel->id;

        $data->save();

        return response()->json([
            'message' => "#$data->id. Ishlab chiqarish jarayoni bekor qilindi",
            'data' => [
                'id' => $data->id,
            ],
        ]);
    }
}
