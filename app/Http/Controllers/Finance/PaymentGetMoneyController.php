<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentGetMoneyRequest;
use App\Services\Payment\PaymentGetMoneyService;
use Illuminate\Http\Request;

class PaymentGetMoneyController extends Controller
{
    public function __construct(
        protected PaymentGetMoneyService $paymentGetMoneyService,
    ) {}

    public function index() {}

    public function store(StorePaymentGetMoneyRequest $request)
    {
        $newPaymentMessage = $this->paymentGetMoneyService->create($request->validated());

        return response()->json([
            "message" => $newPaymentMessage,
        ]);
    }

    public function show(string $id) {}
}
