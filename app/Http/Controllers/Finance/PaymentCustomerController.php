<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentCustomerRequest;

class PaymentCustomerController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StorePaymentCustomerRequest $request)
    {
        dd($request->validated());
    }

    public function show()
    {
        //
    }
}
