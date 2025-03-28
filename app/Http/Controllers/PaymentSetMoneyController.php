<?php

namespace App\Http\Controllers;

use App\Exceptions\ServerErrorException;
use App\Http\Requests\StorePaymentSetMoneyRequest;
use App\Http\Resources\PaymentSetMoneyResource;
use App\Services\Payment\PaymentSetMoneyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentSetMoneyController extends Controller
{
    public function __construct(
        protected PaymentSetMoneyService $paymentSetMoneyService
    )
    {

    }

    public function index(): JsonResponse
    {
        $data = $this->paymentSetMoneyService->findAll();

        return response()->json([
            'data' => PaymentSetMoneyResource::collection($data),
        ]);
    }

    /**
     * @throws ServerErrorException
     */
    public function store(StorePaymentSetMoneyRequest $request): JsonResponse
    {
        $message = $this->paymentSetMoneyService->create($request->validated());

        return response()->json([
            'message' => $message,
        ],201);
    }

    public function show(string $id): JsonResponse
    {
        $data = $this->paymentSetMoneyService->findOne((int)$id);

        return response()->json([
            'data' => PaymentSetMoneyResource::make($data),
        ]);
    }
}
