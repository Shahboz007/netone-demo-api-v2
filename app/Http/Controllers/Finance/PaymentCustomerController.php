<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentCustomerRequest;
use App\Http\Resources\PaymentCustomerResource;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentCustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Payment::with('paymentable', 'user', 'wallets', 'status')
            ->where('paymentable_type', 'App\Models\Customer')
            ->orderBy('created_at', 'desc');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->get();

        return response()->json([
            'data' => PaymentCustomerResource::collection($data),
        ]);
    }

    public function store(StorePaymentCustomerRequest $request): ?JsonResponse
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

            // Increment User Wallets
            foreach ($request->validated('wallet_list') as $wallet) {
                $this->incrementPivotAmount(auth()->id(), $wallet['wallet_id'], $wallet['amount']);
            }

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
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }

    public function show(string $id): JsonResponse
    {
        $query = Payment::with('paymentable', 'user', 'wallets', 'status')
            ->where('paymentable_type', 'App\Models\Customer')
            ->orderBy('created_at', 'desc');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->findOrFail($id);

        return response()->json([
            'data' => PaymentCustomerResource::make($data),
        ]);
    }

    public function incrementPivotAmount($userId, $walletId, $incrementBy): void
    {
         DB::table('user_wallet')
            ->where('user_id', $userId)
            ->where('wallet_id', $walletId)
            ->update([
                'amount' => DB::raw("amount + {$incrementBy}")
            ]);
    }
}
