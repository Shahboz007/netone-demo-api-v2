<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiveProductRequest;
use App\Http\Requests\UpdateReceiveProductRequest;
use App\Models\ProductStock;
use App\Models\ReceiveProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReceiveProductController extends Controller
{
    public function index()
    {
        // Gate
        Gate::authorize('viewAny', ReceiveProduct::class);

        $query = ReceiveProduct::with(
            "user",
            "supplier",
            "product",
            "amountType",
        );

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->latest()->get();

        return response()->json([
            'data' => $data,
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

        DB::beginTransaction();

        try {
            $newReceive = ReceiveProduct::create([
                'user_id' => auth()->id(),
                'supplier_id' => $request->validated('supplier_id'),
                'product_id' => $request->validated('product_id'),
                'amount_type_id' => $productStock->amount_type_id,
                'date_received' => $request->validated('date_received'),
                'amount' => $request->validated('amount')
            ]);

            $productStock->increment('amount', $request->validated('amount'));

            DB::commit();

            return response()->json([
                'message' => "Yuk muvaffaqiyatli qabul qilindi",
                'data' => $newReceive
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->serverError($e);
        }
    }


    public function show($receiveId)
    {
        // Gate
        Gate::authorize('view', ReceiveProduct::class);

        $query = ReceiveProduct::with(
            "user",
            "supplier",
            "product",
            "amountType",
        )->where('id', $receiveId);

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->firstOrFail();

        return response()->json([
            'data' => $data,
        ], 201);
    }


    public function update(UpdateReceiveProductRequest $request, ReceiveProduct $receiveProduct)
    {
        //
    }


    public function destroy(ReceiveProduct $receiveProduct)
    {
        //
    }
}
