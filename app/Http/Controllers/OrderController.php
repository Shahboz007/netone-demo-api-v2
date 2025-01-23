<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderShowResource;
use App\Models\Product;
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
        ];
        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:' . implode(',', $allowedStatuses)],
        ]);

        if (!empty($validated)) {
            $status = Status::where('code', $validated['status'])->firstOrFail();
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
                if ($product['amount_type_id'] === 1) {
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
            'orderDetails'
        );

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $data = $query->firstOrFail();

        return response()->json([
            'data' => OrderShowResource::make($data)
        ]);
    }
}
