<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderReturnRequest;
use App\Http\Requests\UpdateOrderReturnRequest;
use App\Http\Resources\OrderReturnResource;
use App\Http\Resources\OrderReturnShowResource;
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

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->get();

        return response()->json([
            "data" => OrderReturnResource::collection($data),
            "total_sale_price" => (float)$data->sum('total_sale_price'),
            "total_cost_price" => (float)$data->sum('total_cost_price'),
        ]);
    }


    public function store(StoreOrderReturnRequest $request)
    {
        //
    }


    public function show(string $id): JsonResponse
    {
        $query = OrderReturn::with('user', 'orderReturnDetails', 'order');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->findOrFail($id);
        return response()->json([
            "data" => OrderReturnShowResource::make($data),
        ]);
    }


}
