<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishProductionProcessRequest;
use App\Http\Requests\StoreProductionProcessRequest;
use App\Http\Resources\ProductionProcessResource;
use App\Http\Resources\ProductionProcessShowResource;
use App\Models\Product;
use App\Models\ProductionProcess;
use App\Models\ProductStock;
use App\Models\ReceiveProduct;
use App\Models\ReceiveProductDetail;
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
            return $this->mainErrRes("Zaxira mavjud emas");
        }

        foreach ($validateItems as $item) {
            $productName = $pluckProducts->get($item['product_id']);

            if (!$pluckStockItems->has($item['product_id'])) {
                return $this->mainErrRes("`$productName` mahsulotning zaxirasi mavjud emas!");
            }

            if ($pluckStockItems->get($item['product_id']) < $item['amount']) {
                return $this->mainErrRes("`$productName` mahsulotning zaxirasi yetarli emas!");
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

            // Cost Price
            $costPrice = 0;

            // Decrement From Stock
            foreach ($stockItems as $item) {
                // Request Amount
                $amount = $pluckReqProducts->get($item->product_id);

                // Decrement
                $item->decrement('amount', $amount);

                // Get Last Receive Product
                $lastReceive = ReceiveProductDetail::where('product_id', $item->product_id)->latest()->first();
                if($lastReceive){
                    $costPrice += $lastReceive->price;
                }
            }

            // Change Cost Price
            $newProcess->cost_price = $costPrice;
            $newProcess->save();

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
                return $this->mainErrRes('Bu ishlab chiqarish jarayoni allaqachon bekor qilingan');
            } else if ($statusCurrent->code === 'productionStopped') {
                return $this->mainErrRes("Bu ishlab chiqarish jarayoni allaqachon to'xtatilgan");
            } else if ($statusCurrent->code === 'productionCompleted') {
                return $this->mainErrRes("Bu ishlab chiqarish jarayoni allaqachon tayyorlangan");
            }

            return $this->mainErrRes("Bu ishlab chiqarish jarayonini tugallab bo'lmaydi");
        }

        // Status productionCompleted
        $statusProductionCompleted = Status::where('code', 'productionCompleted')->firstOrFail();

        $stock = ProductStock::where('product_id', $productionProcess->productionRecipe->out_product_id)->firstOrFail();
        $outProduct = Product::findOrFail($productionProcess->productionRecipe->out_product_id);

        DB::beginTransaction();

        try {
            $productionProcess->status_id = $statusProductionCompleted->id;
            $productionProcess->out_amount = $request->validated('total_amount');

            // Change stock
            $stock->increment('amount', $productionProcess->out_amount);

            $productionProcess->save();

            // Set cost price
            $outProduct->update(['cost_price' => $productionProcess->cost_price]);
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

        if (!$statusProductionProcess) return $this->mainErrRes("Bu ishlab chiqarish jarayonini bekor qilib bo'lmaydi");

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
