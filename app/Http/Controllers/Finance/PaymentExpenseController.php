<?php

namespace App\Http\Controllers\Finance;

use App\Exceptions\ServerErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\QueryParameterRequest;
use App\Http\Requests\StorePaymentExpenseRequest;
use App\Http\Resources\PaymentExpenseResource;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Status;
use App\Services\Payment\PaymentExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentExpenseController extends Controller
{
    public function __construct(
        protected PaymentExpenseService $paymentExpenseService
    )
    {
    }

    public function index(QueryParameterRequest $request): JsonResponse
    {
        $result = $this->paymentExpenseService->findAll($request->validated());

        return response()->json([
            'data' => PaymentExpenseResource::collection($result['data']),
            'totals' => $result['totals']
        ]);
    }

    /**
     * @throws ServerErrorException
     */
    public function store(StorePaymentExpenseRequest $request): JsonResponse
    {

        $result = $this->paymentExpenseService->create($request->validated());

        return response()->json([
            'message' => $result['message'],
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $result = $this->paymentExpenseService->findOne($id);

        return response()->json([
            'data' => PaymentExpenseResource::make($result['data']),
        ]);
    }
}
