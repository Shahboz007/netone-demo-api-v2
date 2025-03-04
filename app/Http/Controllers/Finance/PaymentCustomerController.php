<?php

namespace App\Http\Controllers\Finance;

use App\Exceptions\ServerErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\QueryParameterRequest;
use App\Http\Requests\StorePaymentCustomerRequest;
use App\Http\Resources\PaymentCustomerResource;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Status;
use App\Models\User;
use App\Services\Payment\PaymentCustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentCustomerController extends Controller
{
    public function __construct(
        protected PaymentCustomerService $paymentCustomerService
    )
    {
    }

    public function index(QueryParameterRequest $request): JsonResponse
    {
        $result = $this->paymentCustomerService->findAll($request->validated());

        return response()->json([
            'data' => PaymentCustomerResource::collection($result['data']),
            'totals' => $result['totals'],
        ]);
    }

    /**
     * @throws ServerErrorException
     */
    public function store(StorePaymentCustomerRequest $request): ?JsonResponse
    {
        $result = $this->paymentCustomerService->create($request->validated());

        return response()->json([
            'message' => $result['message'],
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $result = $this->paymentCustomerService->findOne((int)$id);

        return response()->json([
            'data' => PaymentCustomerResource::make($result['data']),
        ]);
    }


}
