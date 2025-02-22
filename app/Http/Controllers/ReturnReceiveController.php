<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturnReceiveRequest;
use App\Http\Resources\ReturnReceiveResource;
use App\Http\Resources\ReturnReceiveShowResource;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ReturnReceive;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReturnReceiveController extends Controller
{
    public function index(): JsonResponse
    {
        // Gate
        Gate::authorize('viewAny', ReturnReceive::class);

        $data = ReturnReceive::with([
            'user',
            'supplier',
        ])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            "data" => ReturnReceiveResource::collection($data),
        ]);
    }


    public function store(StoreReturnReceiveRequest $request)
    {
        // Gate
        Gate::authorize('create', ReturnReceive::class);

        // Products
        $products = Product::whereIn('id', array_column($request->validated('product_list'), 'product_id'))
            ->get();
        // Pluck Product
        $pluckProductName = $products->pluck('name', 'id');
        $pluckProductsAmountType = $products->pluck('price_amount_type_id', 'id')->toArray();
        $pluckProductsCostPrice = $products->pluck('cost_price', 'id')->toArray();

        // Product Stock
        $productStock = ProductStock::whereIn('id', array_column($request->validated('product_list'), 'polka_id'))->get();
        if ($productStock->isEmpty()) {
            return $this->mainErrRes("Siz tanlagan mahsulotlar uchun zaxira polka topilmadi. Zaxira polkani tekshiring");
        }

        // Pluck Stock
        $pluckStockName = $productStock->pluck('name', 'id')->toArray();
        $pluckStockAmountType = $productStock->pluck('amount_type_id', 'id')->toArray();
        $pluckStockAmount = $productStock->pluck("amount", "id")->toArray();

        foreach ($request->validated('product_list') as $item) {

            // Product Name
            $productName = $pluckProductName[$item['product_id']];

            // Check Stock Exist
            if (!isset($pluckStockAmount[$item['polka_id']])) {
                return $this->mainErrRes("`$productName` mahsulot uchun zaxira polka topilmadi");
            }

            // Stock Name
            $stockName = $pluckStockName[$item['polka_id']];

            // Check Math Amount Type
            if ($pluckStockAmountType[$item['polka_id']] !== $pluckProductsAmountType[$item['product_id']]) {
                return $this->mainErrRes("`$productName` mahsulot o'lchov birligiga `$stockName` o'lchov birligi mos emas");
            }

            if ($pluckStockAmount[$item['polka_id']] < $item['amount']) {
                return $this->mainErrRes("`$productName` mahsulotni qaytarib berishingiz uchun `$stockName` zaxira polkada miqdor yetarli emas");
            }
        }

        // Supplier
        $supplier = Supplier::findOrFail($request->validated('supplier_id'));

        DB::beginTransaction();
        try {
            // New Return
            $newReturn = ReturnReceive::create([
                "user_id" => auth()->id(),
                "supplier_id" => $request->validated('supplier_id'),
                "old_balance" => $supplier->balance,
                "date_received" => $request->validated('date_received'),
                "comment" => $request->validated('comment'),
                "total_sale_price" => 0,
                "total_cost_price" => 0,
            ]);

            $totalSalePrice = 0;
            $totalCostPrice = 0;

            // Create Many items
            $list = [];
            foreach ($request->validated('product_list') as $item) {
                $sumSalePrice = $item['amount'] * $item['price'];
                $sumCostPrice = $item['amount'] * $pluckProductsCostPrice[$item['product_id']];

                $list[] = [
                    'return_receive_id' => $newReturn->id,
                    'product_id' => $item['product_id'],
                    'amount_type_id' => $pluckProductsAmountType[$item['product_id']],
                    'amount' => $item['amount'],
                    'sale_price' => $item['price'],
                    'cost_price' => $pluckProductsCostPrice[$item['product_id']],
                    'sum_sale_price' => $sumSalePrice,
                    'sum_cost_price' => $sumCostPrice,
                ];

                // Totals
                $totalSalePrice += $sumSalePrice;
                $totalCostPrice += $sumCostPrice;

                // Decrement from Stock
                $stockItem = ProductStock::where('id', $item['polka_id'])->where('product_id', $item['product_id'])
                    ->firstOrFail();
                $stockItem->decrement('amount', $item['amount']);
            }
            $newReturn->returnReceiveDetails()->createMany($list);

            // Change Total Price
            $newReturn->total_sale_price = $totalSalePrice;
            $newReturn->total_cost_price = $totalCostPrice;
            $newReturn->save();

            // Change Supplier Balance
            $supplier->decrement('balance', $totalSalePrice);

            DB::commit();

            $formatVal = number_format($totalSalePrice, 2, '.', ',');

            return response()->json([
                "message" => "$supplier->first_name $supplier->last_name taminotchiga $formatVal uzs miqdorida yuk muvaffaqiyatli qaytarildi"
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }


    public function show(int $id)
    {
        // Gate
        Gate::authorize('view', ReturnReceive::class);

        $data = ReturnReceive::with([
            'user',
            'supplier',
            'returnReceiveDetails',
        ])
            ->findOrFail($id);


        return response()->json([
            "data" => ReturnReceiveShowResource::make($data),
        ]);
    }
}
