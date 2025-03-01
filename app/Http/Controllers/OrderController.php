<?php

namespace App\Http\Controllers;

use App\Exceptions\ServerErrorException;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderAddProductRequest;
use App\Http\Requests\UpdateOrderCompletedRequest;
use App\Http\Requests\UpdateOrderSubmittedRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderShowResource;
use App\Models\Order;
use App\Service\Order\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request): JsonResponse
    {
        // Gate
        Gate::authorize('viewAny', Order::class);

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
            'startDate' => 'required|date|date_format:d-m-Y|before_or_equal:endDate',
            'endDate' => 'required|date|date_format:d-m-Y|after_or_equal:startDate',
        ]);

        $status = $validated['status'] ?? null;

        // Date
        $this->orderService->setDate($validated['startDate'], $validated['endDate']);

        // Fetch Date
        $result = $this->orderService->findAll($status);

        // Response
        return response()->json([
            'data' => OrderResource::collection($result['data']),
            'total_sale_price' => $result['total_sale_price'],
        ]);
    }


    /**
     * @throws ServerErrorException
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        // Gate
        Gate::authorize('create', Order::class);

        $result = $this->orderService->create([
            'customer_id' => $request->validated('customer_id'),
            'product_list' => $request->validated('product_list'),
        ]);

        return response()->json($result);
    }


    public function show(Request $request, string $id): JsonResponse
    {
        // Gate
        Gate::authorize('view', Order::class);

        $result = $this->orderService->findOne((int) $id);

        return response()->json([
            'data' => OrderShowResource::make($result['data'])
        ]);
    }

    public function confirm(string $id): JsonResponse
    {
        // Gate
        Gate::authorize('confirm', Order::class);

        $result = $this->orderService->confirm($id);

        return response()->json($result);
    }

    /**
     * @throws ServerErrorException
     */
    public function addProduct(UpdateOrderAddProductRequest $request, string $id): JsonResponse
    {
        Gate::authorize('addProduct', Order::class);

        $result = $this->orderService->addProduct($request->validated(), (int) $id);

        return response()->json($result);
    }

    public function completed(UpdateOrderCompletedRequest $request, string $id): JsonResponse
    {
        // Gate
        Gate::authorize('completed', Order::class);

        $result = $this->orderService->completed($request->validated(), $id);

        return response()->json($result);
    }

    /**
     * @throws ServerErrorException
     */
    public function submitted(UpdateOrderSubmittedRequest $request, string $id): JsonResponse
    {
        // Gate
        Gate::authorize('submit', Order::class);

        $result = $this->orderService->submit($request->validated(), (int) $id);

        return response()->json($result);
    }
}
