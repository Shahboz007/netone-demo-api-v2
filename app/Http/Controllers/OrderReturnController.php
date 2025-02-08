<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderReturnRequest;
use App\Http\Requests\UpdateOrderReturnRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderReturn;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\assertDirectoryDoesNotExist;

class OrderReturnController extends Controller
{
    public function index()
    {
        //
    }


    public function store(StoreOrderReturnRequest $request)
    {
        // Validation Order Details
        $orderDetails = OrderDetail::where('order_id', $request->validated('order_id'))
            ->whereIn('id', array_column($request->validated('order_item_list'), 'order_item_id'))
            ->get();

        $pluckItems = $orderDetails->pluck('amount', 'id')->toArray();

        foreach ($request->validated('order_item_list') as $item) {
            if (!isset($pluckItems[$item['order_item_id']])) {
                return $this->mainErrRes("Buyurtmaga tegishli bo'lmagan mahsulotni tanladingiz");
            }
        }

        // Current Order
        $order = Order::with('orderDetails')->findOrFail($request->validated('order_id'));
        $statusSubmitted = Status::where('code', 'orderSubmitted')->firstOrFail();
        if ($order->status_id != $statusSubmitted->id) {
            return $this->mainErrRes('Bu buyurtma allaqachon qaytarilgan');
        }

        // Status Order Returned
        $statusOrderReturned = Status::where('code', 'orderReturned')->firstOrFail();

        DB::beginTransaction();
        try {
            // Create New Order Return
            $newOrderReturn = OrderReturn::create([
                'user_id' => auth()->id(),
                'order_id' => $request->validated('order_id'),
                'total_sale_price' => 0,
                'total_cost_price' => 0,
                'comment' => $request->validated('comment'),
            ]);

            // Attach Items to Order Returned
            $list = [];
            foreach ($request->validated('order_item_list') as $item) {
                $list[] = [
                    'order_return_id' => $newOrderReturn->id,
                    'order_detail_id' => $item['order_item_id'],
                    'amount' => $item['amount'],
                    'amount_type_id' => $item['amount_type_id'],
                ];
            }
            $newOrderReturn->orderReturnDetails()->createMany($list);

            // Change Of Order And Details item status
            if (count($list) == $order->orderDetails->count()) {
                $order->status_id = $statusOrderReturned->id;
            }
            foreach ($list as $item) {
                DB::table('order_details')
                    ->where('id', $item['order_detail_id'])
                    ->where('order_id', $order->id)
                    ->update(['status_id' => $statusOrderReturned->id]);
            }


            DB::commit();

            if (count($list) == $order->orderDetails->count()) {
                return response()->json([
                    'message' => "#$order->id buyurtmaning mahsulotlari muvaffaqiyatli to'liq qaytarildi",
                ]);
            }


            $count = count($list);

            return response()->json([
                'message' => "#$order->id buyurtmaning $count ta mahsulot muvaffaqiyatli qaytarildi",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }


    public function show(OrderReturn $orderReturn)
    {
        //
    }


    public function update(UpdateOrderReturnRequest $request, OrderReturn $orderReturn)
    {
        //
    }


    public function destroy(OrderReturn $orderReturn)
    {
        //
    }
}
