<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentSupplierRequest;
use App\Http\Resources\PaymentSupplierResource;
use App\Models\Payment;
use App\Models\Status;
use App\Models\Supplier;
use App\Models\UserWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentSupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Payment::with(['paymentable', 'user', 'wallets', 'status'])
            ->select('payments.*', DB::raw("SUM(payment_wallet.sum_price) as total_price"))
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->where('paymentable_type', 'App\Models\Supplier')
            ->groupBy('payments.id', 'payments.paymentable_type', 'payments.created_at')
            ->orderBy('payments.created_at', 'desc');


        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->get();

        return response()->json([
            'data' => PaymentSupplierResource::collection($data),
        ]);
    }

    public function store(StorePaymentSupplierRequest $request)
    {
        // Supplier
        $supplier = Supplier::findOrFail($request->validated('supplier_id'));

        // Validation User Wallet
        $userAllWallets = UserWallet::where('user_id', auth()->id())->get();
        $pluckWalletAmount = $userAllWallets->pluck('amount', 'wallet_id')->toArray();
        $pluckWalletName = $userAllWallets->pluck('name', 'wallet_id')->toArray();

        foreach ($request->validated('wallet_list') as $walletItem) {
            if ($pluckWalletAmount[$walletItem['wallet_id']] && $pluckWalletAmount[$walletItem['wallet_id']] < $walletItem['amount']) {
                $walletName = $pluckWalletName[$walletItem['wallet_Id']];
                abort(422, "`$walletName` bu hisobingizda mablag' yetarli emas! Hisobingizni tekshiring");
            }
        }

        // Status Supplier Payment
        $statusSupplierPayment = Status::where('code', 'paymentSupplier')->firstOrFail();

        DB::beginTransaction();

        try {
            // New Payment
            $newPayment = new Payment([
                    'user_id' => auth()->id(),
                    'status_id' => $statusSupplierPayment->id,
                    'comment' => $request->validated('comment')]
            );

            $supplier->payments()->save($newPayment);

            // Attach to Wallet
            $walletAttachList = [];
            foreach ($request->validated('wallet_list') as $wallet) {
                $walletAttachList[$wallet['wallet_id']] = [
                    'amount' => $wallet['amount'],
                    'rate_amount' => $wallet['rate_amount'],
                    'sum_price' => $wallet['amount'] * $wallet['rate_amount'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }

            $newPayment->wallets()->attach($walletAttachList);

            // Convert To uzs
            $sum = 0;
            foreach ($walletAttachList as $wallet) {
                $sum += $wallet['amount'] * $wallet['rate_amount'];
            }

            // Change User Wallet Amount
            foreach ($request->validated('wallet_list') as $item) {
                DB::table('user_wallet')
                    ->where('user_id', auth()->id())
                    ->where('wallet_id', $item['wallet_id'])
                    ->update(['amount' => DB::raw("amount - {$item['amount']}")]);
            }

            // Change Supplier Balance
            $supplier->decrement('balance', $sum);

            DB::commit();

            return response()->json([
                "message" => "Taminotchiga o'zkazma muvaffaqiyatli o'tkazildi",
            ],201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }

    public function show(string $id): JsonResponse
    {
        $query = Payment::with(['paymentable', 'user', 'wallets', 'status'])
            ->select('payments.*', DB::raw("SUM(payment_wallet.sum_price) as total_price"))
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->where('paymentable_type', 'App\Models\Supplier')
            ->groupBy('payments.id', 'payments.paymentable_type', 'payments.created_at')
            ->orderBy('payments.created_at', 'desc');


        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->findOrFail($id);

        return response()->json([
            'data' => PaymentSupplierResource::make($data),
        ]);
    }
}
