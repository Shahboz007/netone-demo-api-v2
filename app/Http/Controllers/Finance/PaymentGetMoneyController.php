<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentGetMoneyRequest;
use App\Http\Resources\PaymentGetMoneyResource;
use App\Services\Payment\PaymentGetMoneyService;

class PaymentGetMoneyController extends Controller
{
    public function __construct(
        protected PaymentGetMoneyService $paymentGetMoneyService,
    ) {}

    public function index()
    {
        $data = $this->paymentGetMoneyService->findAll();

        // return ($data);
        return response()->json([
            "data" => PaymentGetMoneyResource::collection($data),
        ]);
    }

    public function store(StorePaymentGetMoneyRequest $request)
    {
        $newPaymentMessage = $this->paymentGetMoneyService->create($request->validated());

        return response()->json([
            "message" => $newPaymentMessage,
        ]);
    }

    public function show(string $id)
    {
        $data = $this->paymentGetMoneyService->findOne((int) $id);

        return response()->json([
            "data" => PaymentGetMoneyResource::make($data),
        ]);
    }
}
