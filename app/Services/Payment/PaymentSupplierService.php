<?php

namespace App\Services\Payment;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ServerErrorException;
use App\Models\Payment;
use App\Models\Supplier;
use App\Models\UserWallet;
use App\Services\Status\StatusService;
use App\Services\Utils\DateFormatter;
use Illuminate\Support\Facades\DB;

class PaymentSupplierService
{
    public function findAll(array $data): array
    {
        $startDate = DateFormatter::format($data['startDate'], 'start');
        $endDate = DateFormatter::format($data['endDate'], 'end');
        $supplierId = $data['supplier_id'] ?? null;

        $query = Payment::with(['paymentable', 'user', 'wallets', 'status'])
            ->select('payments.*', DB::raw("SUM(payment_wallet.sum_price) as total_price"))
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->where('paymentable_type', 'App\Models\Supplier')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->groupBy('payments.id', 'payments.paymentable_type', 'payments.created_at')
            ->orderBy('payments.created_at', 'desc');

        if ($supplierId) {
            $query->where('payments.paymentable_id', $supplierId);
        }

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        // Data
        $data = $query->get();

        // Totals
        $totals = $this->getTotals($supplierId, [$startDate, $endDate]);

        return [
            'data' => $data,
            'totals' => $totals,
        ];
    }

    /**
     * @throws ServerErrorException
     * @throws InvalidDataException
     */
    public function create(array $data): array
    {
        // Data
        $supplierId = $data['supplier_id'];
        $reqWalletList = $data['wallet_list'];
        $comment = $data['comment'] ?? null;

        // Supplier
        $supplier = Supplier::findOrFail($supplierId);

        // Validation User Wallet
        $userAllWallets = UserWallet::where('user_id', auth()->id())->get();
        $pluckWalletAmount = $userAllWallets->pluck('amount', 'wallet_id')->toArray();
        $pluckWalletName = $userAllWallets->pluck('wallet', 'wallet_id')->toArray();
        foreach ($reqWalletList as $item) {
            if ($pluckWalletAmount[$item['wallet_id']] && $pluckWalletAmount[$item['wallet_id']] < $item['amount']) {
                $walletName = $pluckWalletName[$item['wallet_id']]['name'];
                throw new InvalidDataException("`$walletName` bu hisobingizda mablag' yetarli emas! Hisobingizni tekshiring");
            }
        }

        // Status Supplier Payment
        $statusSupplierPayment = StatusService::findByCode('paymentSupplier');

        DB::beginTransaction();

        try {
            // New Payment
            $newPayment = new Payment([
                'user_id' => auth()->id(),
                'status_id' => $statusSupplierPayment->id,
                'comment' => $comment,
            ]);

            $supplier->payments()->save($newPayment);

            // Sum Amount
            $sumAmount = 0;

            // Attach to Wallet
            $walletAttachList = [];
            foreach ($reqWalletList as $wallet) {
                $sumPrice = $wallet['amount'] * $wallet['rate_amount'];
                $walletAttachList[$wallet['wallet_id']] = [
                    'amount' => $wallet['amount'],
                    'rate_amount' => $wallet['rate_amount'],
                    'sum_price' => $sumPrice,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];

                // Decrement User Wallet
                $this->decrementUserWallet($wallet['wallet_id'], $wallet['amount']);

                // Increment Sum Amount
                $sumAmount += $sumPrice;
            }

            $newPayment->wallets()->attach($walletAttachList);

            // Change Supplier Balance
            $supplier->decrement('balance', $sumAmount);

            DB::commit();

            $formatSum = number_format($sumAmount, 2, '.', ',');

            return [
                'message' => "Taminotchiga $formatSum so'm  muvaffaqiyatli o'tkazildi",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServerErrorException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function fineOne(int $supplierId): array
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

        $data = $query->findOrFail($supplierId);

        return [
            'data' => $data,
        ];
    }

    private function getTotals(string|null $supplierId, array $date): array
    {
        $query = DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select(
                DB::raw('SUM(payment_wallet.sum_price) as total_amount'),
                DB::raw('COUNT(payments.id) as total_count')
            )
            ->where('paymentable_type', 'App\Models\Supplier');

        if ($supplierId) {
            $query->where('payments.paymentable_id', $supplierId);
        }

        $query->whereBetween('payments.created_at', [$date[0], $date[1]])
            ->first();

        $data = $query->first();

        return [
            "total_amount" => (float)$data->total_amount,
            "total_count" => (int)$data->total_count,
        ];
    }

     private function decrementUserWallet($walletId, $amount): void
    {
        $userId = auth()->id();
        $userWallet =DB::table('user_wallet')
            ->where('user_id', $userId)
            ->where('wallet_id', $walletId)
            ->firstOrFail();
        $userWallet->decrement($amount);
    }
}
