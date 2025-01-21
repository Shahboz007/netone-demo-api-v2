<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\ProductStock;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Gate
        Gate::authorize('viewAny', Order::class);

        $query = Order::with(
            'user.roles',
            'customer',
            'product',
            'amountType'
        );

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $data = $query->get();

        return response()->json([
            'data' => OrderResource::collection($data)
        ]);
    }


    public function store(StoreOrderRequest $request)
    {
        // Gate
        Gate::authorize('create', Order::class);

        // New Order status
        $newOrderStatus = Status::where('code', 'orderNew')->firstOrFail();

        DB::beginTransaction();

        try {
            $newOrder = Order::create([
                "user_id" => auth()->id(),
                "customer_id" => $request->validated('customer_id'),
                "status_id" => $newOrderStatus->id,
            ]);


            $newOrder->orderDetails()->createMany($request->validated('product_list'));

            DB::commit();

            return response()->json([
                "message" => "Yangi buyurtma muvaffaqiyatli qo'shildi!",
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }


    public function show(Request $request, string $id)
    {
        // Gate
        Gate::authorize('view', Order::class);

        $query = Order::with(
            'user.roles',
            'customer',
            'product',
            'amountType'
        );

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $data = $query->get();

        return response()->json([
            'data' => OrderResource::collection($data)
        ]);
    }


    public function update(UpdateOrderRequest $request, string $id)
    {
        // Gate
        Gate::authorize('update', Order::class);

        // Get Order
        $query = Order::with(
            'user.roles',
            'customer',
            'product',
            'amountType'
        );

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $order = $query->where('id', $id)->firstOrFail();

        // Check Order Status for New Order
        $isNew = Status::where('code', 'orderNew')->where('id', $order->status_id)->exists();
        if (!$isNew) abort(422, "Buyurtmani o'zgartirish mumkin emas! Allaqachon buyurtma ishlab chiqarish jarayonida");

        $order->update($request->validated());

        return response()->json([
            "message" => "Buyurtma muvaffaqiyatli tahrirlandi!",
            "data" => OrderResource::make($order),
        ]);
    }
}
