<?php

namespace App\Http\Controllers;

use App\Models\OrderCancel;
use App\Http\Requests\StoreOrderCancelRequest;
use App\Http\Resources\OrderCancelResource;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderCancelController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderCancel::with(
            'user.roles',
            'order.product',
            'order.amountType',
            'order.status'
        );

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $data = $query->get();

        return response()->json([
            'data' => OrderCancelResource::collection($data)
        ]);
    }


    public function store(StoreOrderCancelRequest $request)
    {
        $order = Order::findOrFail($request->validated('order_id'));

        // Check status
        $orderStatus = Status::findOrFail($order->status_id);
        $this->checkOrderStatus($orderStatus->code);

        // Cancel Status of Order 
        $orderCancelStatus = Status::where('code', 'orderCancel')->firstOrFail();

        DB::beginTransaction();
        try {
            // New Cancel Order
            $newCancel = OrderCancel::create([
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'comment' => $request->validated('order_id')
            ]);

            // Change Order Status
            $order->status_id = $orderCancelStatus->id;
            $order->save();

            DB::commit();

            return response()->json([
                "message" => "Buyurtma bekor qilindi",
                "data" => $newCancel,
            ], 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->serverError();
        }
    }


    public function show(OrderCancel $orderCancel)
    {
        //
    }

    private function checkOrderStatus(string $code)
    {
        if ($code !== 'orderNew') {
            switch ($code) {
                case 'orderInProgress':
                    abort(422, "Buyurtmani bekor qilib bo'lmaydi! Buyurtma ishlab chiqarish jarayonida");
                case 'orderCancel':
                    abort(422, "Buyurtma allaqachon berkor qilingan!");
                case 'orderCompleted':
                    abort(422, "Buyurtma allaqachon topshirilgan!");
                default:
                    abort(422, "Buyurtmani bekor qilib bo'lmaydi!");
            }
        }
    }
}
