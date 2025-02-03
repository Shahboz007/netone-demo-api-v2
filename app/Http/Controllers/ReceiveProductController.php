<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiveProductRequest;
use App\Http\Resources\ReceiveProductResource;
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


        $productStock = ProductStock::where('product_id', $request->validated('product_id'))
            ->first();

        if (!$productStock) {
            abort(422, 'Bu mahsulot uchun zaxira polka ochilmagan. Adminka zaxira polka yaratishi kerak');
        }

        // Supplier
        $supplier = Supplier::findOrFail($request->validated('supplier_id'));

        // Calc Total Price
        $totalPrice = $request->validated('price') * $request->validated('amount');

        // Status Receive Debt
        $statusReceiveDebt = Status::where('code', 'receiveProductDebt')->firstOrFail();

        DB::beginTransaction();

        try {
            // New Receive
            $newReceive = ReceiveProduct::create([
                'user_id' => auth()->id(),
                'supplier_id' => $supplier->id,
                'product_id' => $request->validated('product_id'),
                'amount_type_id' => $productStock->amount_type_id,
                'status_id' => $statusReceiveDebt->id,
                'date_received' => $request->validated('date_received'),
                'amount' => $request->validated('amount'),
                'price' => $request->validated('price'),
                'total_price' => $totalPrice,
                'comment' => $request->validated('comment'),
            ]);

            // Change Stock Amount
            $productStock->increment('amount', $request->validated('amount'));

            // Change of Supplier's balance
            $supplier->increment('balance', $totalPrice);

            DB::commit();

            $receivedAmount = $request->validated('amount');
            $receivedPrice = $request->validated('price');

            return response()->json([
                'message' => "Yuk muvaffaqiyatli qabul qilindi. Jami $receivedAmount x $receivedPrice = $totalPrice uzs.",
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
