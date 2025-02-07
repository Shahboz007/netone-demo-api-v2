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
        $reqProductIdList = array_column($request->validated('product_list'), 'product_id');

        // Check Product Stock
        $productStockList = ProductStock::whereIn('product_id', $reqProductIdList)->get();

        if ($productStockList->isEmpty()) {
            abort(422, 'Bu mahsulot uchun zaxira polka ochilmagan. Adminka zaxira polka yaratishi kerak');
        }

        $pluckedProductStockList = $productStockList->pluck('amount', 'product_id');

        foreach ($request->validated('product_list') as $item) {
            if (!$pluckedProductStockList[$item['product_id']]) {
                abort(422, 'Bu mahsulot uchun zaxira polka ochilmagan. Adminka zaxira polka yaratishi kerak');
            }

            if ($pluckedProductStockList[$item['product_id']] < $item['amount']) {
                return $this->mainErrRes("Zaxira yetarli emas!");
            }
        }

        // Supplier
        $supplier = Supplier::findOrFail($request->validated('supplier_id'));

        // Status Receive Debt
        $statusReceiveDebt = Status::where('code', 'receiveProductDebt')->firstOrFail();

        // Products
        $products = Product::with('priceAmountType')->with('')->where('id', $reqProductIdList)->get();
        $pluckedProductsName = $products->pluck('productAmountType', 'id')->toArray();
        dd($pluckedProductsName);

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

            // Attach Details
            $productList = [];

            foreach ($request->validated('product_list') as $item) {
                $productName =

                    $productList[] = [
                        'product_id' => $item['product_id'],
                        'amount_type_id' => $item['amount'],
                        'price' => $item['price']
                    ];
            }

            //            $newReceive->receiveProductDetails()->createMany();

            // Change Stock Amount
            //            $productStockList->increment('amount', $request->validated('amount'));

            // Change of Supplier's balance
            //            $supplier->increment('balance', $totalPrice);

            //            DB::commit();

            //            $receivedAmount = $request->validated('amount');
            //            $receivedPrice = $request->validated('price');

            //            return response()->json([
            //                'message' => "Yuk muvaffaqiyatli qabul qilindi. Jami $receivedAmount x $receivedPrice = $totalPrice uzs.",
            //                'data' => $newReceive
            //            ], 201);
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
