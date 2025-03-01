<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiveProductRequest;
use App\Http\Resources\ReceiveProductResource;
use App\Http\Resources\ReceiveProductShowResource;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ReceiveProduct;
use App\Models\Status;
use App\Models\Supplier;
use App\Services\Receive\ReceiveProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReceiveProductController extends Controller
{
    public function __construct(
        protected ReceiveProductService $receiveProductService,
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        // Gate
        Gate::authorize('viewAny', ReceiveProduct::class);

        $validated = $request->validate([
            'startDate' => 'required|date|date_format:d-m-Y|before_or_equal:endDate',
            'endDate' => 'required|date|date_format:d-m-Y|after_or_equal:startDate',
        ]);
        // Date
        $this->receiveProductService->setDate($validated['startDate'], $validated['endDate']);

        $result = $this->receiveProductService->findAll();

        return response()->json([
            'data' => ReceiveProductResource::collection($result['data']),
            'total_price' => $result['total_price'],
            'total_count' => $result['total_count'],
        ]);
    }


    public function store(StoreReceiveProductRequest $request): JsonResponse
    {
        // Gate
        Gate::authorize('create', ReceiveProduct::class);

       $result = $this->receiveProductService->create($request->validated());
       return response()->json($result, 201);
    }


    public function show(string $id): JsonResponse
    {
        // Gate
        Gate::authorize('view', ReceiveProduct::class);

        $result = $this->receiveProductService->findOne((int) $id);

        return response()->json([
            'data' => ReceiveProductShowResource::make($result['data']),
        ]);
    }
}
