<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderReturnRequest;
use App\Http\Requests\UpdateOrderReturnRequest;
use App\Http\Resources\OrderReturnResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderReturn;
use App\Models\ProductStock;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\assertDirectoryDoesNotExist;

class OrderReturnController extends Controller
{
    public function index(): JsonResponse
    {
        $query = OrderReturn::with('user', 'order.customer', 'order.user');

        if(!auth()->user()->isAdmin()){
            $query->where('user_id', auth()->id());
        }

        $data = $query->get();

        return response()->json([
            "data" => OrderReturnResource::collection($data),
            "total_sale_price" => (float) $data->sum('total_sale_price'),
            "total_cost_price" => (float) $data->sum('total_cost_price'),
        ]);
    }


    public function store(StoreOrderReturnRequest $request)
    {
        // Current Order
        $currentOrder = Order::with('orderDetails')->findOrFail($request->validated('order_id'));
        $pluckCurrentOrderItemsAmount = $currentOrder->orderDetails->pluck('completed_amount', 'id');

        // Validate Current Order Status
        $statusOrderSubmitted = Status::where('code', 'orderSubmitted')->firstOrFail();
        if ($currentOrder->status_id !== $statusOrderSubmitted->id) {
            return $this->mainErrRes("Buyurtma noto'g'ri tanlanmoqda");
        }

        // Validate Current Order Items with Request items to match
        foreach ($request->validated('order_item_list') as $item) {
            if (isset($pluckCurrentOrderItemsAmount[$item['item_id']])) {
                if ($pluckCurrentOrderItemsAmount[$item['item_id']] < $item['amount']) {
                    return $this->mainErrRes("Qaytarilayotgan mahsulotlar miqdori, buyurtma qilingan mahsulot miqdoridan ko'p. Qilinayotgan ish bo'yicha adminga xabar beriladi");
                }
            } else {
                return $this->mainErrRes("Buyurtma mahsulotini to'g'ri tanladingiz");
            }
        }

        // Pluck Sale Price and Cost price of Current Order items
        $pluckSalePriceList = $currentOrder->orderDetails->pluck('sale_price', 'id');
        $pluckCostPriceList = $currentOrder->orderDetails->pluck('cost_price', 'id');

        // Product Stock
        $productStock = ProductStock::select('id', 'product_id', 'amount')
            ->whereIn('id', array_column($request->validated('order_item_list'), 'polka_id'))
            ->get();
        $pluckProductStock = $productStock->pluck('product_id', 'id')->toArray();
        $pluckCurrentOrderItemsProduct = $currentOrder->orderDetails->pluck('product', 'id')->toArray();

        // Validate Request polka with Product Stock Polka
        foreach ($request->validated('order_item_list') as $item) {
            if (isset($pluckProductStock[$item['polka_id']])) {
                if ($pluckProductStock[$item['polka_id']] !== $pluckCurrentOrderItemsProduct[$item['item_id']]['id']) {
                    $name = $pluckCurrentOrderItemsProduct[$item['item_id']]['name'];
                    return $this->mainErrRes("`$name` mahsulotning polkasi noto'g'ri tanlanmoqda");
                }
            } else {
                return $this->mainErrRes("Mahsulot polkasi noto'g'ri tanlanmoqda");
            }
        }

        // Customer
        $customer = Customer::findOrFail($currentOrder->customer_id);

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
            foreach ($request->validated('order_item_list') as $item) {
                $sumCostPrice = $item['amount'] * $pluckCostPriceList[$item['item_id']];
                $sumSalePrice = $item['amount'] * $pluckSalePriceList[$item['item_id']];

                $itemsList[] = [
                    'order_return_id' => $newOrderReturn->id,
                    'order_detail_id' => $item['item_id'],
                    'amount' => $item['amount'],
                    'amount_type_id' => $item['amount_type_id'],
                    'cost_price' => $pluckCostPriceList[$item['item_id']],
                    'sale_price' => $pluckSalePriceList[$item['item_id']],
                    'sum_cost_price' => $sumCostPrice,
                    'sum_sale_price' => $sumSalePrice,
                ];

                $totalCostPrice += $sumCostPrice;
                $totalSalePrice += $sumSalePrice;

                // Increment Polka Stock
                DB::table('product_stocks')
                    ->where('id', $item['polka_id'])
                    ->increment('amount', $item['amount']);
            }
            $newOrderReturn->orderReturnDetails()->createMany($itemsList);

            // Update New Return Order Price
            $newOrderReturn->total_cost_price = $totalCostPrice;
            $newOrderReturn->total_sale_price = $totalSalePrice;
            $newOrderReturn->save();

            // Increment Customer Balance
            $customer->increment('balance', $totalSalePrice);

            // Finish
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
