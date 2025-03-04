<?php

namespace App\Services\Payment;

use App\Exceptions\ServerErrorException;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\UserWallet;
use App\Services\Status\StatusService;
use App\Services\Utils\DateFormatter;
use Illuminate\Support\Facades\DB;

class PaymentCustomerService
{
    public function findAll(array $data): array
    {
        $startDate = DateFormatter::format($data['startDate'], 'start');
        $endDate = DateFormatter::format($data['endDate'], 'end');
        $customerId = $data['customer_id'] ?? null;

        $query = Payment::with(['paymentable', 'user', 'wallets', 'status'])
            ->select('payments.*', DB::raw('SUM(payment_wallet.sum_price) as total_price'))
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->where('paymentable_type', 'App\Models\Customer')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->groupBy('payments.id', 'payments.paymentable_type', 'payments.created_at')
            ->orderBy('payments.created_at', 'desc');

        if ($customerId) {
            $query->where('payments.paymentable_id', $customerId);
        }


        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        //  Data
        $data = $query->get();

        // Totals
        $totals = $this->getTotals($customerId, [$startDate, $endDate]);

        return [
            'data' => $data,
            'totals' => $totals,
        ];
    }

    /**
     * @throws ServerErrorException
     */
    public function create(array $data): array
    {
        // Data
        $customerId = $data['customer_id'];
        $reqWalletList = $data['wallet_list'];
        $comment = $data['comment'] ?? null;

        // Customer
        $customer = Customer::findOrFail($customerId);

        // Status Payment Debt Customer
        $statusDebtCustomer = StatusService::findByCode('paymentCustomer');

        DB::beginTransaction();

        try {
            // New Payment For Customer
            $payment = new Payment([
                "user_id" => auth()->id(),
                'status_id' => $statusDebtCustomer->id,
                "comment" => $comment,
            ]);

            $customer->payments()->save($payment);

            // Convert To uzs
            $sumAmount = 0;

            // Attach Wallets
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

                // Increment User Wallet
                $this->incrementUserWallet($wallet['wallet_id'], $wallet['amount']);

                // Increment Sum Amount
                $sumAmount += $sumPrice;
            }

            $payment->wallets()->attach($walletAttachList);

            // Change Customer Balance
            $customer->increment('balance', $sumAmount);

            DB::commit();

            $formatSum = number_format($sumAmount, 2, '.', ',');

            return [
                'message' => "$customer->first_name $customer->last_name mijozdan $formatSum uzs o'tkazma muvaffaqiyatli qabul qilindi! Mijozning balansini tekshiring"
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServerErrorException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function findOne(int $id): array
    {
        $query = Payment::with(['paymentable', 'user', 'wallets', 'status'])
            ->select('payments.*', DB::raw('SUM(payment_wallet.sum_price) as total_price'))
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->where('paymentable_type', 'App\Models\Customer')
            ->groupBy('payments.id', 'payments.paymentable_type', 'payments.created_at')
            ->orderBy('payments.created_at', 'desc');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->findOrFail($id);

        return [
            'data' => $data,
        ];
    }

    private function getTotals(string|null $customerId, array $date): array
    {
        $query = DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select(
                DB::raw('SUM(payment_wallet.sum_price) as total_amount'),
                DB::raw('COUNT(payments.id) as total_count')
            )
            ->where('paymentable_type', 'App\Models\Customer');

        if ($customerId) {
            $query->where('payments.paymentable_id', $customerId);
        }

        $query->whereBetween('payments.created_at', [$date[0], $date[1]])
            ->first();

        $data = $query->first();

        return [
            "total_amount" => (float)$data->total_amount,
            "total_count" => (int)$data->total_count,
        ];
    }

    private function incrementUserWallet($walletId, $amount): void
    {
        $userId = auth()->id();
        $userWallet = UserWallet::where('wallet_id', $walletId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $userWallet->increment('amount', $amount);
    }
}
