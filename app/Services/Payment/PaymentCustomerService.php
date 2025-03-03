<?php

namespace App\Services\Payment;

use App\Models\Payment;
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

    public function create()
    {

    }

    public function findOne()
    {

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
}
