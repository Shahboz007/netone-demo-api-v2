<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ServerErrorException;
use App\Http\Requests\QueryParameterRequest;
use App\Http\Requests\StorePaymentSupplierRequest;
use App\Http\Resources\PaymentSupplierResource;
use App\Services\Payment\PaymentSupplierService;
use Illuminate\Http\JsonResponse;

class PaymentSupplierController extends Controller
{
    public function __construct(
        protected PaymentSupplierService $paymentSupplierService
    )
    {
    }

    public function index(QueryParameterRequest $request): JsonResponse
    {
        $result = $this->paymentSupplierService->findAll($request->validated());

        return response()->json([
            'data' => PaymentSupplierResource::collection($result['data']),
            'totals' => $result['totals'],
        ]);
    }

    /**
     * @throws ServerErrorException
     * @throws InvalidDataException
     */
    public function store(StorePaymentSupplierRequest $request): JsonResponse
    {
        $result =  $this->paymentSupplierService->create($request->validated());
        return response()->json([
                "message" => $result['message'],
            ], 201);
    }

    public function show(string $id): JsonResponse
    {

        $result = $this->paymentSupplierService->fineOne((int)$id);

        return response()->json([
            'data' => PaymentSupplierResource::make($result['data']),
        ]);
    }
}
