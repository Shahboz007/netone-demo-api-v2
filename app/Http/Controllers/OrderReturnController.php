<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderReturnRequest;
use App\Http\Resources\OrderReturnResource;
use App\Http\Resources\OrderReturnShowResource;
use App\Models\AmountType;
use App\Models\Customer;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderReturnController extends Controller
{
    public function index()
    {
        $query = OrderReturn::with('user', 'customer');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            "data" => OrderReturnResource::collection($data),
            "total_sale_price" => (float)$data->sum('total_sale_price'),
            "total_cost_price" => (float)$data->sum('total_cost_price'),
        ]);
    }


    public function store(StoreOrderReturnRequest $request)
    {
        // Products
        $products = Product::whereIn('id', array_column($request->validated('product_list'), 'product_id'))->get();
        $pluckProductName = $products->pluck('name', 'id');
        $pluckProductsSalePrice = $products->pluck('sale_price', 'id');
        $pluckProductsCostPrice = $products->pluck('sale_price', 'id');

        // Stock
        $stock = ProductStock::whereIn('id', array_column($request->validated('product_list'), 'polka_id'))->get();
        $pluckStock = $stock->pluck('product_id', 'id');
        $pluckStockName = $stock->pluck('name', 'id');
        $pluckStockAmounType = $stock->pluck('amount_type_id', 'id');

        // Amount Types
        $amountType = AmountType::whereIn('id', array_column($request->validated('product_list'), 'amount_type_id'))->get();
        $pluckAmontTypeName = $amountType->pluck('name', 'id');

        foreach ($request->validated('product_list') as $item) {

            if (isset($pluckStock[$item['polka_id']])) {
                // Check match
                if ($pluckStock[$item['polka_id']] !== $item['product_id']) {


                    $stockName = $pluckStockName[$item['polka_id']];
                    $productName = $pluckProductName[$item['product_id']];

                    return $this->mainErrRes("`$productName` mahsulot uchun `$stockName` nomli polka tegishli emas. Polkani to'g'ri tanlang");
                }

                // Check match amoun type with stock
                if ($pluckStockAmounType[$item['polka_id']] !== $item['amount_type_id']) {
                    $amountTypeName = $pluckAmontTypeName[$item['amount_type_id']];
                    $polkaName = $pluckStockName[$item['polka_id']];
                    $correctPolkaName = $pluckAmontTypeName[$pluckStockAmounType[$item['polka_id']]];

                    return $this->mainErrRes("`$amountTypeName` o'lchov biriligi, `$polkaName` nomli polka uchun mos emas. `$polkaName` nomli polka faqat `$correctPolkaName` o'lchov biriligini qabul qiladi");
                }
            } else {
                return $this->mainErrRes("Polka topilmadi");
            }
        }

        // Customer
        $customer = Customer::findOrFail($request->validated('customer_id'));


        DB::beginTransaction();
        try {
            // Create New Return Order
            $newReturnOrder = OrderReturn::create([
                'user_id' => auth()->id(),
                'customer_id' => $request->validated('customer_id'),
                'total_sale_price' => 0,
                'total_cost_price' => 0,
                'comment' => $request->validated('comment')
            ]);

            $totalSalePrice = 0;
            $totalCostPrice = 0;


            // Create Many Details Item
            $list = [];

            foreach ($request->validated('product_list') as $item) {
                $costPrice = $pluckProductsCostPrice[$item['product_id']];
                $salePrice = $pluckProductsSalePrice[$item['product_id']];

                $sumCostPrice = $costPrice * $item['amount'];
                $sumSalePrice = $salePrice * $item['amount'];

                $list[] = [
                    'order_return_id' => $newReturnOrder->id,
                    'product_id' => $item['product_id'],
                    'polka_id' => $item['polka_id'],
                    'amount_type_id' => $item['amount_type_id'],
                    'amount' => $item['amount'],
                    'cost_price' => $costPrice,
                    'sale_price' => $salePrice,
                    'sum_cost_price' => $sumCostPrice,
                    'sum_sale_price' => $sumSalePrice,
                ];

                $totalCostPrice += $sumCostPrice;
                $totalSalePrice += $sumSalePrice;

                // Change Stock Amount
                $stockItem = ProductStock::where('id', $item['polka_id'])
                    ->where('product_id', $item['product_id'])->firstOrFail();

                $stockItem->increment('amount', $item['amount']);
            };

            $newReturnOrder->orderReturnDetails()->createMany($list);

            // Change Totals
            $newReturnOrder->total_cost_price = $totalCostPrice;
            $newReturnOrder->total_sale_price = $totalSalePrice;

            $newReturnOrder->save();

            // Change Customer Balance
            $customer->increment('balance', $totalSalePrice);

            // Finish
            DB::commit();

            $formatVal = number_format($totalSalePrice, 2, '.', ',');

            return response()->json([
                "message" => "Qaytarilgan yuk muvaffaqiyatli qabul qilindi. Jami summa $formatVal uzs"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }


    public function show(string $id): JsonResponse
    {
        $query = OrderReturn::with('user', 'customer', 'orderReturnDetails');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->findOrFail($id);

        return response()->json([
            "data" => OrderReturnShowResource::make($data),
        ]);
    }
}
