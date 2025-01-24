<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderCompletedRequest;
use App\Http\Requests\UpdateOrderSubmittedRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderShowResource;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Status;
use App\Models\SubmittedOrder;
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
                $query->with('submittedOrder');
            }else if($validated['status'] === 'orderCancel') {
                $query->with('cancelOrder');
            }

            $query->where('status_id', $status->id);
        }

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


    public function show(Request $request, string $id)
    {
        // Gate
        Gate::authorize('view', Order::class);

        $query = Order::with(
            'user',
            'customer',
            'status',
            'orderDetails',
            'cancelOrder',
            'submittedOrder'
        );

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $data = $query->firstOrFail();

        return response()->json([
            'data' => OrderShowResource::make($data)
        ]);
    }

    public function confirm(string $id)
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
    public function completed(UpdateOrderCompletedRequest $request, string $id)
    {

        $order = Order::with('orderDetails')->findOrFail($id);

        // Status Code
        $statusCode = 'orderCompleted';
        $StatusCompleted = Status::where('code', $statusCode)->firstOrFail();

        //******* Validation Order Detials Item *******//
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

        DB::beginTransaction();

        try {

            // Add Order Details completed amounts
            foreach ($order->orderDetails as $detail) {
                if ($updates->has($detail->product_id)) {
                    $detail->update(['completed_amount' => $updates->get($detail->product_id)]);
                }
            }

            // Change Order Status
            $order->status_id = $StatusCompleted->id;
            $order->save();

            DB::commit();
            return response()->json([
                'message' => "Buyurtma topshirishga tayyor, hozirgi holati tayyorlandi",
                'data' => [
                    'status' => $StatusCompleted
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }

    public function submitted(UpdateOrderSubmittedRequest $request, string $id)
    {
        $order = Order::with('orderDetails')->findOrFail($id);

        // Validation Order Status
        $statusCompleted = Status::where('code', 'orderCompleted')->firstOrFail();
        if ($order->status_id !== $statusCompleted->id) {
            abort(422, "Bu buyurtma topshirish uchun tayyor emas! Buyurtma egasiga topshirilishi uchun tayyorlandi holatida bo'lishi kerak!");
        }

        // Status Code
        $StatusSubmitted = Status::where('code', 'orderSubmitted')->firstOrFail();

        // Customer
        $customer = Customer::findOrFail($order->customer_id);

        DB::beginTransaction();

        try {
            // Create New Submitted Order
            $newSubmittedOrder = SubmittedOrder::create([
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'comment' => $request->validated('comment'),
                'total_cost_price' => 0,
                'total_sale_price' => 0,
            ]);

            $totalCostPrice = 0;
            $totalSalePrice = 0;

            foreach ($order->orderDetails as $detail) {
                // Qop
                if ($detail->amount_type_id === 2) {
                    $totalCostPrice += $detail->completed_amount * $detail->product->cost_price;
                    $totalSalePrice += $detail->completed_amount * $detail->product->sale_price;
                } else {
                    abort(422, "Buyurtmani topshirish uchun buyurtma mahsulotlarining o'lchov birligi qopda bo'lishi kerak!");
                }
            }

            // Change Total Price
            $newSubmittedOrder->total_cost_price = $totalCostPrice;
            $newSubmittedOrder->total_sale_price = $totalSalePrice;
            $newSubmittedOrder->save();

            // Order
            $order->status_id = $StatusSubmitted->id;
            $order->save();

            // Customer
            $customer->balance -= $totalSalePrice;
            $customer->save();

            DB::commit();
            return response()->json([
                'message' => "Buyurtma muvaffaqiyatli topshirildi",
                'data' => [
                    'status' => $StatusSubmitted
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }
}
