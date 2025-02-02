<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentExpenseRequest;
use App\Http\Resources\PaymentExpenseResource;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentExpenseController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Payment::with('paymentable', 'user', 'wallets', 'status')
            ->where('paymentable_type', 'App\Models\Expense')
            ->orderBy('created_at', 'desc');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->get();

        return response()->json([
            'data' => PaymentExpenseResource::collection($data),
        ]);
    }

    public function store(StorePaymentExpenseRequest $request): ?JsonResponse
    {
        $reqAmount = $request->validated('amount');

        // Validation User Wallet
        $userWallet = auth()->user()->wallets()->wherePivot('wallet_id', $request->validated('wallet_id'))->firstOrFail();

        if ($userWallet->pivot->amount < $reqAmount) {
            abort(422, "`$userWallet->name` bu hisobingizda mablag' yetarli emas! Hisobingizni tekshiring");
        }

        // Expense
        $expense = Expense::findOrFail($request->validated('expense_id'));

        // Status Payment Expense
        $statusPaymentExpense = Status::where('code', 'paymentExpense')->firstOrFail();

        DB::beginTransaction();

        try {
            // New Payment For Expense
            $payment = new Payment([
                'user_id' => auth()->id(),
                'status_id' => $statusPaymentExpense->id,
                'comment' => $request->validated('comment')
            ]);
            $expense->payments()->save($payment);

            // Attach Amount To Wallet
            $payment->wallets()->attach($request->validated('wallet_id'), [
                'amount' => $reqAmount,
                'rate_amount' => $request->validated('rate_amount'),
                'sum_price' => $reqAmount * $request->validated('rate_amount'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Decrement from User Wallet
            DB::table('user_wallet')
                ->where('user_id', auth()->id())
                ->where('wallet_id', $request->validated('wallet_id'))
                ->update(['amount' => DB::raw("amount - {$reqAmount}")]);

            // Finish
            DB::commit();

            // Amount Currency
            $currency = $userWallet->currency->symbol;

            $formatNum = number_format($reqAmount, 2, '.', ',');

            return response()->json([
                'message' => "`$expense->name` xarajat uchun $formatNum $currency  muvaffaqiyatli o'tkazildi!",
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }

    }

    public function show(string $id): JsonResponse
    {
        $query = Payment::with('paymentable', 'user', 'wallets', 'status')
            ->where('paymentable_type', 'App\Models\Expense')
            ->orderBy('created_at', 'desc');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->findOrFail($id);

        return response()->json([
            'data' => PaymentExpenseResource::make($data),
        ]);
    }
}
