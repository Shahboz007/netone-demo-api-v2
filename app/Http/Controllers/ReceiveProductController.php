<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiveProductRequest;
use App\Http\Resources\ReceiveProductResource;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ReceiveProduct;
use App\Models\Status;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReceiveProductController extends Controller
{
    public function index(): JsonResponse
    {
        // Gate
        Gate::authorize('viewAny', ReceiveProduct::class);

        $query = ReceiveProduct::with(
            "user",
            "supplier",
            "product",
            "amountType",
            "status"
        );

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->latest()->get();

        return response()->json([
            'data' => ReceiveProductResource::collection($data),
        ]);
    }


    public function store(StoreReceiveProductRequest $request)
    {
        // Gate
        Gate::authorize('create', ReceiveProduct::class);

        // Req Product Plucked List
        $reqPolkaArr = array_column($request->validated('product_list'), 'polka_id');

        // Stock
        $stockList = ProductStock::whereIn('id', $reqPolkaArr)->get();
        $stockAmountArr = $stockList->pluck('amount', 'id')->toArray();

        if ($stockList->isEmpty()) {
            return $this->mainErrRes('Mahsulotlar uchun zaxira polka ochilmagan. Admin zaxira polka yaratishi kerak');
        }

        // Products
        $products = Product::whereIn('id', array_column($request->validated('product_list'), 'product_id'))->get();
        $pluckedProductsName = $products->pluck('name', 'id')->toArray();
        $pluckedProductsPriceAmountType = $products->pluck('price_amount_type_id', 'id')->toArray();

        // Stock Items
        foreach ($request->validated('product_list') as $item) {
            if (!isset($stockAmountArr[$item['polka_id']])) {
                $productName = $pluckedProductsName[$item['product_id']];
                return $this->mainErrRes("`$productName` mahsulot uchun zaxira polka ochilmagan. Adminka zaxira polka yaratishi kerak");
            }

        }


        // Supplier
        $supplier = Supplier::findOrFail($request->validated('supplier_id'));

        // Status Receive Debt
        $statusReceiveDebt = Status::where('code', 'receiveProductDebt')->firstOrFail();

        DB::beginTransaction();

        try {
            // New Receive
            $newReceive = ReceiveProduct::create([
                'user_id' => auth()->id(),
                'supplier_id' => $supplier->id,
                'status_id' => $statusReceiveDebt->id,
                'date_received' => $request->validated('date_received'),
                'total_price' => 0,
                'comment' => $request->validated('comment'),
            ]);
            $totalPrice = 0;

            // Attach Details
            $productList = [];

            foreach ($request->validated('product_list') as $item) {
                $sum = $item['amount'] * $item['price'];

                $productList[] = [
                    'receive_product_id' => $newReceive->id,
                    'product_id' => $item['product_id'],
                    'amount' => $item['amount'],
                    'price' => $item['price'],

                    'amount_type_id' => $pluckedProductsPriceAmountType[$item['product_id']],
                    'status_id' => $statusReceiveDebt->id,
                    'sum_price' => $sum,
                ];

                $totalPrice += $sum;
            }

            $newReceive->receiveProductDetails()->createMany($productList);

            $newReceive->total_price = $totalPrice;
            $newReceive->save();

            // Change Stock Amount
            foreach ($request->validated('product_list') as $item) {
                DB::table('product_stocks')
                    ->where('id', $item['polka_id'])
                    ->where('product_id', $item['product_id'])
                    ->increment('amount', $item['amount']);
            }

            // Change of Supplier's balance
            $supplier->increment('balance', $totalPrice);
            DB::commit();

            $formatVal = number_format($totalPrice, 2, '.', ',');

            return response()->json([
                'message' => "Yuk muvaffaqiyatli qabul qilindi. Jami $formatVal uzs.",
                'data' => $newReceive
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->serverError($e);
        }
    }


    public function show($receiveId): JsonResponse
    {
        // Gate
        Gate::authorize('view', ReceiveProduct::class);

        $query = ReceiveProduct::with(
            "user",
            "supplier",
            "product",
            "amountType",
            "status"
        )->where('id', $receiveId);

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->firstOrFail();

        return response()->json([
            'data' => ReceiveProductResource::make($data),
        ]);
    }
}
