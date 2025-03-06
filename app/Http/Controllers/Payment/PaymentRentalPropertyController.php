<?php

namespace App\Http\Controllers\Payment;

use App\Exceptions\ServerErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRentalPropertyRequest;
use App\Services\Payment\PaymentRentalPropertiesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentRentalPropertyController extends Controller
{
    public function __construct(
        protected PaymentRentalPropertiesService $paymentRentalPropertiesService
    )
    {
    }

    public function index()
    {
        $result = $this->paymentRentalPropertiesService->findAll();

        return response()->json([
            'data' => $result['data'],
        ]);
    }

    /**
     * @throws ServerErrorException
     */
    public function store(StorePaymentRentalPropertyRequest $request): JsonResponse
    {
        $result = $this->paymentRentalPropertiesService->create($request->validated());

        return response()->json([
            'message' => $result['message'],
        ],201);
    }

    public function show()
    {

    }
}
