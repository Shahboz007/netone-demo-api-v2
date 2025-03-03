<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueryParameterRequest;
use App\Http\Requests\StorePaymentSupplierRequest;
use App\Http\Resources\PaymentSupplierResource;
use App\Models\Payment;
use App\Models\Status;
use App\Models\Supplier;
use App\Models\UserWallet;
use App\Services\Payment\PaymentSupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentSupplierController extends Controller
{
    public function __construct(
        protected PaymentSupplierService $paymentSupplierService
    )
    {
    }

    public function index(QueryParameterRequest $request): JsonResponse
    {
        $result = $this->paymentSupplierService->findAll($request->validated());

        return response()->json([
            'data' => PaymentSupplierResource::collection($result['data']),
            'totals' => $result['totals'],
        ]);
    }

    public function store(StorePaymentSupplierRequest $request)
    {
        // Supplier
        $supplier = Supplier::findOrFail($request->validated('supplier_id'));

        // Validation User Wallet
        $userAllWallets = UserWallet::where('user_id', auth()->id())->get();
        $pluckWalletAmount = $userAllWallets->pluck('amount', 'wallet_id')->toArray();
        $pluckWalletName = $userAllWallets->pluck('wallet', 'wallet_id')->toArray();
        foreach ($request->validated('wallet_list') as $item) {
            if ($pluckWalletAmount[$item['wallet_id']] && $pluckWalletAmount[$item['wallet_id']] < $item['amount']) {
                $walletName = $pluckWalletName[$item['wallet_id']]['name'];
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

            $formatSum = number_format($sum, 2, '.', ',');

            return response()->json([
                "message" => "Taminotchiga $formatSum so'm  muvaffaqiyatli o'tkazildi",
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }

    public function show(string $id): JsonResponse
    {

        $result = $this->paymentSupplierService->fineOne((int)$id);

        return response()->json([
            'data' => PaymentSupplierResource::make($result['data']),
        ]);
    }
}
