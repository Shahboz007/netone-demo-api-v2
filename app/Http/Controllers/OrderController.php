<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\ProductStock;
use App\Models\Status;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
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
        $productStock = ProductStock::where('product_id', $request->validated('product_id'))->firstOr();

        // New Order status
        $newOrderStatus = Status::where('code', 'orderNew')->firstOrFail();
        
        $newOrder = Order::create([
            "user_id" => auth()->id(),
            "customer_id" => $request->validated('customer_id'),
            "product_id" => $request->validated('product_id'),
            "amount_type_id" => $productStock->amount_type_id,
            "status_id" => $newOrderStatus->id,
            "amount" => $request->validated('amount'),
        ]);

        return response()->json([
            "message" => "Yangi buyurtma muvaffaqiyatli qo'shildi!",
            "data" => OrderResource::make($newOrder),
        ], 201);
    }


    public function show(Request $request, string $id)
    {

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


    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }


    public function destroy(Order $order)
    {
        //
    }
}
