<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentCustomerRequest;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentCustomerController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StorePaymentCustomerRequest $request)
    {
        // Customer
        $customer = Customer::findOrFail($request->validated('customer_id'));

        // Status Payment Debt Customer
        $statusDebtCustomer = Status::where('code', 'paymentCustomer')->firstOrFail();

        DB::beginTransaction();

        try {
            // New Payment For Customer
            $payment = new Payment([
                "user_id" => auth()->id(),
                'status_id' => $statusDebtCustomer->id,
                "comment" => $request->validated('comment'),
            ]);

            $customer->payments()->save($payment);

            // Attach Wallets
            $walletAttachList = [];
            foreach ($request->validated('wallet_list') as $wallet) {
                $walletAttachList[$wallet['wallet_id']] = [
                    'amount' => $wallet['amount'],
                    'rate_amount' => $wallet['rate_amount'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }

            $payment->wallets()->attach($walletAttachList);

            // Convert To uzs
            $sum = 0;
            foreach ($walletAttachList as $wallet) {
                $sum += $wallet['amount'] * $wallet['rate_amount'];
            }

            // Change Customer Balance
            $customer->increment('balance', $sum);

            DB::commit();

            return response()->json([
                'message' => "$customer->first_name $customer->last_name mijozdan o'tkazma muvaffaqiyatli qabul qilindi! Mijozning balansini tekshiring",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }

    public function show()
    {
        //
    }
}
