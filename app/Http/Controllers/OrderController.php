<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderAddProductRequest;
use App\Http\Requests\UpdateOrderCompletedRequest;
use App\Http\Requests\UpdateOrderSubmittedRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderShowResource;
use App\Models\CompletedOrder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Status;
use App\Services\GenerateOrderCode;
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

        // Generate Order Code
        $orderCode = GenerateOrderCode::generate($request->validated('customer_id'));

        DB::beginTransaction();

        try {
            $newOrder = Order::create([
                "user_id" => auth()->id(),
                "customer_id" => $request->validated('customer_id'),
                "status_id" => $newOrderStatus->id,
                'total_cost_price' => 0,
                'total_sale_price' => 0,
                'ord_code' => $orderCode
            ]);


            $totalCostPrice = 0;
            $totalSalePrice = 0;

            // Create Order Details
            $detailItemList = [];
            foreach ($request->validated('product_list') as $item) {
                $costPrice = $pluckedCostPrice[$item['product_id']];
                $salePrice = $pluckedSalePrice[$item['product_id']];
                $sumCostPrice = $costPrice * $item['amount'];
                $sumSalePrice = $salePrice * $item['amount'];

                $detailItemList[] = [
                    'product_id' => $item['product_id'],
                    'amount' => $item['amount'],
                    'amount_type_id' => $item['amount_type_id'],
                    'cost_price' => $costPrice,
                    'sale_price' => $salePrice,
                    'sum_cost_price' => $sumCostPrice,
                    'sum_sale_price' => $sumSalePrice,
                ];

                $totalCostPrice += $sumCostPrice;
                $totalSalePrice += $sumSalePrice;
            }

            $newOrder->orderDetails()->createMany($detailItemList);


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
        // Gate
        Gate::authorize('confirm', Order::class);

        $order = Order::where('user_id', auth()->id())->findOrFail($id);

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

    public function addProduct(UpdateOrderAddProductRequest $request, string $id): JsonResponse
    {
        Gate::authorize('addProduct', Order::class);

        // Product
        $product = Product::findOrFail($request->validated('product_id'));

        // Status
        $statusOrderInProgress = Status::where('code', 'orderInProgress')->firstOrFail();

        // Order
        $order = Order::where('user_id', auth()->id())
            ->where('status_id', $statusOrderInProgress->id)
            ->findOrFail($id);


        // Create Order Details Item
        $sumCostPrice = $product->cost_price * $request->validated('amount');
        $sumSalePrice = $product->sale_price * $request->validated('amount');

        DB::beginTransaction();

        try {
            $order->orderDetails()->create([
                'product_id' => $request->validated('product_id'),
                'amount' => $request->validated('amount'),
                'amount_type_id' => $request->validated('amount_type_id'),
                'cost_price' => $product->cost_price,
                'sale_price' => $product->sale_price,
                'sum_cost_price' => $sumCostPrice,
                'sum_sale_price' => $sumSalePrice,
            ]);


            dd($sumSalePrice);
            $order->increment('total_cost_price', $sumCostPrice);
            $order->increment('total_sale_price', $sumSalePrice);

            DB::commit();

            return response()->json([
                'message' => "#$order->id buyurtmaga `$product->name` nomli mahsulot muvaffaqiyatli qo'shildi"
            ]);
        } catch (\Exception $e) {
            return $this->serverError($e);
        }
    }

    public function completed(UpdateOrderCompletedRequest $request, string $id)
    {
        // Gate
        Gate::authorize('completed', Order::class);

        $order = Order::with('orderDetails')->findOrFail($id);

        // Validation Order Status
        $allowedCodes = ['orderNew', 'orderInProgress'];
        $allowedStatuses = Status::whereIn('code', $allowedCodes)->get();
        if (empty($allowedStatuses)) return $this->mainErrRes("Ichki xatolik yuz berdi, iltimos biz bilan bog'laning!");
        if (!$allowedStatuses->contains('id', $order->status_id)) return $this->mainErrRes("Bu buyurtmani tayyorlandi holatiga o'tkazish mumkin emas!");

        //*******--start-- Validation Order Details Item *******//
        $updates = collect($request->validated('product_list'))
            ->pluck('completed_amount', 'product_id');

        // Length
        $orderItemLength = $order->orderDetails()->count();
        $prodItemLength = count($updates);

        if ($orderItemLength > $prodItemLength || $orderItemLength < $prodItemLength) {
            return $this->mainErrRes("Siz buyurtma mahsulotlarni noto'g'ri kiritmoqdasiz, iltimos etiborli bo'ling.");
        }

        foreach ($order->orderDetails as $detail) {
            if (!$updates->has($detail->product_id)) {
                return $this->mainErrRes("Siz buyurtma mahsulotlarni not'g'ri kiritmoqdasiz, iltimos etiborli bo'ling.");
            }
        }
        //*******--end-- Validation Order Details Item *******//
        $reqProductsId = array_column($request->validated('product_list'), 'product_id');

        // Validate Product Stock
        $stockList = ProductStock::with('product')->whereIn('product_id', $reqProductsId)
            ->get();

        if ($stockList->isEmpty()) {
            return $this->mainErrRes("Buyurtmani tayyorlab bo'lmadi. Zaxirani tekshiring!");
        }

        $productPluckList = Product::whereIn('id', $reqProductsId)->select('id', 'name')->get()->pluck('name', 'id');
        $stockAmountPluckList = $stockList->pluck('amount', 'product_id');

        foreach ($request->validated('product_list') as $item) {
            if (!$stockAmountPluckList->has($item['product_id'])) {
                $productName = $productPluckList->get($item['product_id']);
                return $this->mainErrRes("`$productName` mahsuloti bo'yicha zaxira mavjud emas!");
            }

            if ($stockAmountPluckList->get($item['product_id']) < $item['completed_amount']) {
                $productName = $productPluckList->get($item['product_id']);
                return $this->mainErrRes("`$productName` mahsuloti bo'yicha zaxira yetarli emas!");
            }
        }

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
                'customer_old_balance' => 0,
            ]);

            $totalCostPrice = 0;
            $totalSalePrice = 0;

            // Add Order Details completed amounts
            foreach ($order->orderDetails as $detail) {
                if ($updates->has($detail->product_id)) {
                    $completedAmount = $updates->get($detail->product_id);

                    // Calc Total Prices
                    $totalCostPrice += $completedAmount * $detail->product->cost_price;
                    $totalSalePrice += $completedAmount * $detail->product->sale_price;


                    // Update Order details item
                    $detail->update(['completed_amount' => $completedAmount]);

                    $stock = ProductStock::findOrFail($detail->product_id);
                    $stock->decrement('amount', $completedAmount);
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
        // Gate
        Gate::authorize('submit', Order::class);

        $order = Order::with('orderDetails')->findOrFail($id);

        // Validation Order Status
        $statusCompleted = Status::where('code', 'orderCompleted')->firstOrFail();
        if ($order->status_id !== $statusCompleted->id) {
            return $this->mainErrRes("Bu buyurtma topshirish uchun tayyor emas! Buyurtma egasiga topshirilishi uchun tayyorlandi holatida bo'lishi kerak!");
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
            $completedOrder->customer_old_balance = $order->customer->balance;
            $completedOrder->save();


            // Customer
            $customer->decrement('balance', $completedOrder->total_sale_price);

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
