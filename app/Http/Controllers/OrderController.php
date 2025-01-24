<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderCompletedRequest;
use App\Http\Requests\UpdateOrderSubmittedRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderShowResource;
use App\Models\CompletedOrder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Gate
        Gate::authorize('viewAny', Order::class);

        $query = Order::with(
            'user',
            'customer',
            'status'
        );

        // Status
        $allowedStatuses = [
            'orderNew',
            'orderInProgress',
            'orderCancel',
            'orderCompleted',
            'orderSubmitted',
        ];
        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:' . implode(',', $allowedStatuses)],
        ]);

        if (!empty($validated)) {
            $status = Status::where('code', $validated['status'])->firstOrFail();

            // Submitted
            if ($validated['status'] === 'orderSubmitted') {
                $query->with('completedOrder');
            } else if ($validated['status'] === 'orderCancel') {
                $query->with('cancelOrder');
            }

            $query->where('status_id', $status->id);
        }

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $data = $query->orderByDesc('created_at')->get();

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

        // Get Request Products
        $productsId = array_column($request->validated('product_list'), 'product_id');
        $products = Product::whereIn('id', $productsId)
            ->get();

        $pluckedCostPrice = $products->pluck('cost_price', 'id');
        $pluckedSalePrice = $products->pluck('sale_price', 'id');

        DB::beginTransaction();

        try {
            $newOrder = Order::create([
                "user_id" => auth()->id(),
                "customer_id" => $request->validated('customer_id'),
                "status_id" => $newOrderStatus->id,
                'total_cost_price' => 0,
                'total_sale_price' => 0
            ]);


            $totalCostPrice = 0;
            $totalSalePrice = 0;

            foreach ($request->validated('product_list') as $product) {
                // Qop
                if ($product['amount_type_id'] === 2) {
                    $totalCostPrice += $pluckedCostPrice[$product['product_id']] * $product['amount'];
                    $totalSalePrice += $pluckedSalePrice[$product['product_id']] * $product['amount'];
                } else {
                    abort(422, 'Kechirasiz, siz faqat qopda sota olasiz!');
                }
            }

            $newOrder->orderDetails()->createMany($request->validated('product_list'));


            $newOrder->total_cost_price = $totalCostPrice;
            $newOrder->total_sale_price = $totalSalePrice;

            $newOrder->save();

            DB::commit();

            return response()->json([
                "message" => "Yangi buyurtma muvaffaqiyatli qo'shildi!",
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }


    public function show(Request $request, string $id): JsonResponse
    {
        // Gate
        Gate::authorize('view', Order::class);

        $query = Order::with(
            'user',
            'customer',
            'status',
            'orderDetails',
            'cancelOrder',
            'completedOrder'
        )->where('id', $id);

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $data = $query->firstOrFail();

        return response()->json([
            'data' => OrderShowResource::make($data)
        ]);
    }

    public function confirm(string $id): JsonResponse
    {
        $order = Order::findOrFail($id);

        // Status Code
        $statusCode = 'orderInProgress';

        $StatusInProgress = Status::where('code', $statusCode)->firstOrFail();

        $order->status_id = $StatusInProgress->id;
        $order->save();

        return response()->json([
            'message' => "Buyurtma tasdiqlandi va hozir jarayonda",
            'data' => [
                'status' => $StatusInProgress
            ]
        ]);
    }

    public function completed(UpdateOrderCompletedRequest $request, string $id): JsonResponse
    {

        $order = Order::with('orderDetails')->findOrFail($id);

        // Validation Order Status
        $allowedCodes = ['orderNew', 'orderInProgress'];
        $allowedStatuses = Status::whereIn('code', $allowedCodes)->get();
        if (empty($allowedStatuses)) abort(500, "Ichki xatolik yuz berdi, iltimos biz bilan bog'laning!");
        if (!$allowedStatuses->contains('id', $order->status_id)) abort(422, "Bu buyurtmani tayyorlandi holatiga o'tkazish mumkin emas!");

        //*******--start-- Validation Order Detials Item *******//
        $updates = collect($request->validated('product_list'))
            ->pluck('completed_amount', 'product_id');

        // Length
        $orderItemLength = $order->orderDetails()->count();
        $prodItemLength = count($updates);

        if ($orderItemLength > $prodItemLength || $orderItemLength < $prodItemLength) {
            abort(422, "Siz tayyorlangan mahsulotlarni noto'g'ri kiritmoqdasiz, iltimos etiborli bo'ling.");
        }

        foreach ($order->orderDetails as $detail) {
            if (!$updates->has($detail->product_id)) {
                abort(422, "Siz tayyorlangan mahsulotlarni not'g'ri kiritmoqdasiz, iltimos etiborli bo'ling.");
            }
        }
        //*******--end-- Validation Order Details Item *******//

        // Status Code
        $statusCode = 'orderCompleted';
        $statusCompleted = Status::where('code', $statusCode)->firstOrFail();

        DB::beginTransaction();

        try {
            // Create New Completed Order
            $newCompletedOrder = CompletedOrder::create([
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'status_id' => $statusCompleted->id,
                'comment' => $request->validated('comment'),
                'total_cost_price' => 0,
                'total_sale_price' => 0,
            ]);

            $totalCostPrice = 0;
            $totalSalePrice = 0;

            // Add Order Details completed amounts
            foreach ($order->orderDetails as $detail) {
                if ($updates->has($detail->product_id)) {
                    $completedAmount = $updates->get($detail->product_id);

                    // Calc Total Prices
                    if ($detail->amount_type_id === 2) {// Qop
                        $totalCostPrice += $completedAmount * $detail->product->cost_price;
                        $totalSalePrice += $completedAmount * $detail->product->sale_price;
                    } else {
                        abort(422, "Buyurtmani tayyor holatga o'tkazish uchun, buyurtma mahsulotlarining o'lchov birligi qopda bo'lishi kerak!");
                    }

                    // Update Order details item
                    $detail->update(['completed_amount' => $completedAmount]);
                }
            }

            // Change Total Price Of Completed Order
            $newCompletedOrder->total_cost_price = $totalCostPrice;
            $newCompletedOrder->total_sale_price = $totalSalePrice;
            $newCompletedOrder->save();

            // Change Order Status
            $order->status_id = $statusCompleted->id;
            $order->save();

            DB::commit();
            return response()->json([
                'message' => "Buyurtma topshirishga tayyor, hozirgi holati tayyorlandi",
                'data' => [
                    'status' => $statusCompleted
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }

    public function submitted(UpdateOrderSubmittedRequest $request, string $id): JsonResponse
    {
        $order = Order::with('orderDetails')->findOrFail($id);

        // Validation Order Status
        $statusCompleted = Status::where('code', 'orderCompleted')->firstOrFail();
        if ($order->status_id !== $statusCompleted->id) {
            abort(422, "Bu buyurtma topshirish uchun tayyor emas! Buyurtma egasiga topshirilishi uchun tayyorlandi holatida bo'lishi kerak!");
        }

        // Status Code
        $statusSubmitted = Status::where('code', 'orderSubmitted')->firstOrFail();

        // Customer
        $customer = Customer::findOrFail($order->customer_id);

        // Completed Order
        $completedOrder = CompletedOrder::where('order_id', $order->id)->firstOrFail();

        DB::beginTransaction();

        try {

            // Order
            $order->status_id = $statusSubmitted->id;
            $order->save();

            // Completed Order
            $completedOrder->submitted_comment = $request->validated('comment');
            $completedOrder->status_id = $statusSubmitted->id;
            $completedOrder->save();


            // Customer
            $customer->balance -= $completedOrder->total_sale_price;
            $customer->save();

            DB::commit();
            return response()->json([
                'message' => "Buyurtma muvaffaqiyatli topshirildi! Mijoz balansini tekshiring",
                'data' => [
                    'status' => $statusSubmitted
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }
}
