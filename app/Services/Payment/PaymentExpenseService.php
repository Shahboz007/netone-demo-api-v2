<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Services\Utils\DateFormatter;
use Illuminate\Support\Facades\DB;

class PaymentExpenseService
{
    public function findAll(array $data): array
    {
        $startDate = DateFormatter::format($data['startDate'], 'start');
        $endDate = DateFormatter::format($data['endDate'], 'end');
        $expenseId = $data['expense_id'] ?? null;

        $query = Payment::with('paymentable', 'user', 'wallets', 'status')
            ->where('paymentable_type', 'App\Models\Expense')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        // All data
        $data = $query->get();

        // Totals
        $totals = $this->getTotals($expenseId, [$startDate, $endDate]);

        return [
            'data' => $data,
            'totals' => $totals,
        ];
    }

    public function findOne()
    {

    }

    public function create(array $data)
    {

    }

    private function getTotals(string|null $expenseId, array $date): array
    {
        $query = DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select(
                DB::raw('SUM(payment_wallet.sum_price) as total_amount'),
                DB::raw('COUNT(payments.id) as total_count')
            )
            ->where('paymentable_type', 'App\Models\Expense');

        if ($expenseId) {
            $query->where('payments.paymentable_id', $expenseId);
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
