<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentSetMoneyRequest;
use App\Http\Requests\StoreSetMoneyRequest;
use Illuminate\Http\Request;
use PaymentSetMoneyService;

class SetMoneyController extends Controller
{
    public function __construct(
        protected PaymentSetMoneyService $paymentSetMoneyService,
    ){}

    public function store(StorePaymentSetMoneyRequest $request)
    {
        // $this->payment->set
    }
}
