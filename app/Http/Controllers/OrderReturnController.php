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
        $data = OrderReturn::all();

        return $data;
    }


    public function store(StoreOrderReturnRequest $request)
    {
        // Current Order
        $currentOrder = Order::with('orderDetails')->findOrFail($request->validated('order_id'));
        $pluckCurrentOrderItems = $currentOrder->orderDetails->pluck('amount', 'id');

        // Validate Current Order Status
        $statusOrderSubmitted = Status::where('code', 'orderSubmitted')->firstOrFail();
        if ($currentOrder->status_id !== $statusOrderSubmitted->id) {
            return $this->mainErrRes("Buyurtma noto'g'ri tanlanmoqda");
        }

        // Validate Current Order Items with Request items to match
        foreach ($request->validated('order_item_list') as $item) {
            if (isset($pluckCurrentOrderItems[$item['item_id']])) {
                if ($pluckCurrentOrderItems[$item['item_id']] < $item['amount']) {
                    return $this->mainErrRes("Qaytarilayotgan mahsulotlar miqdori, buyurtma qilingan mahsulot miqdoriddan ko'p. Qilinayotgan ish bo'yicha adminga xabar beriladi");
                }
            } else {
                return $this->mainErrRes("Buyurtma mahsulotini to'g'ri tanladingiz");
            }
        }

        // Pluck Sale Price and Cost price of Current Order items
        $pluckSalePriceList = $currentOrder->orderDetails->pluck('sale_price', 'id');
        $pluckCostPriceList = $currentOrder->orderDetails->pluck('cost_price', 'id');

        dd('render');
        DB::beginTransaction();
        try {
            // New Return Order
            $newOrderReturn = OrderReturn::create([
                'user_id' => auth()->id(),
                'order_id' => $currentOrder->id,
                'comment' => $request->validated('comment'),
                'total_sale_price' => 0,
                'total_cost_price' => 0,
            ]);

            // Totals
            $totalCostPrice = 0;
            $totalSalePrice = 0;

            // Attach Order Items AND Change Stock Amount
            $itemsList = [];
            foreach ($request->validated('product_list') as $item) {
                $sumCostPrice = $item['amount'] * $pluckCostPriceList[$item['item_id']];
                $sumSalePrice = $item['amount'] * $pluckSalePriceList[$item['item_id']];

                $itemsList[] = [
                    'order_return_id' => $newOrderReturn->id,
                    'order_details_id' => $item['item_id'],
                    'amount' => $item['amount'],
                    'amount_type_id' => $item['amount_type_id'],
                    'cost_price' => $pluckCostPriceList[$item['item_id']],
                    'sale_price' => $pluckSalePriceList[$item['item_id']],
                    'sum_cost_price' => $sumCostPrice,
                    'sum_sale_price' => $sumSalePrice,
                ];

                $totalCostPrice += $sumCostPrice;
                $totalSalePrice += $sumSalePrice;
            }
            $newOrderReturn->orderReturnDetails()->createMany($itemsList);

            // Update New Return Order Price
            $newOrderReturn->total_cost_price = $totalCostPrice;
            $newOrderReturn->total_sale_price = $totalSalePrice;
            $newOrderReturn->save();

            // Finish🚀
            DB::commit();
            return response()->json([
                "message" => "Qaytarilgan mahsulotlar muvaffaqiyatli qabul qilindi",
            ]);

        } catch (\Exception $e) {
            DB::rollback();
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
