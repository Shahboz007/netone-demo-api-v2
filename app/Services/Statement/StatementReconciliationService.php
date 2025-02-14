<?php

namespace App\Services\Statement;

use App\Models\Status;
use Illuminate\Support\Facades\DB;

class StatementReconciliationService
{
    public function getAll()
    {
        // Status
        $statusSubmittedOrder = Status::where('code', "orderSubmitted")->firstOrFail();
        $statusPaymentCustomer = Status::where('code', 'paymentCustomer')->firstOrFail();

        $data = DB::table('orders')
            ->join('completed_orders', 'orders.id', '=', 'completed_orders.order_id')
            ->join('statuses', 'orders.status_id', '=', 'statuses.id')
            ->leftJoin('payments', 'orders.customer_id', '=', 'payments.paymentable_id')
            ->leftJoin('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select(
                DB::raw('COUNT(completed_orders.id) as count_orders'),
                DB::raw('SUM(completed_orders.total_sale_price) as amount_orders'),
                DB::raw('0 as count_payments'),
                DB::raw('0 as amount_payments'),
                DB::raw('completed_orders.updated_at as action_date'),
            )
            ->where('completed_orders.updated_at', '!=', null)
            ->where('payments.created_at', '!=', null)
            ->where('completed_orders.status_id', $statusSubmittedOrder->id)
            ->where('payments.paymentable_type', 'App\Models\Customer')
            ->where('payments.status_id', $statusPaymentCustomer->id)
            ->groupBy('action_date')
            ->union(
                DB::table('payments')
                    ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
                    ->select(
                        DB::raw('0 as count_orders'),
                        DB::raw('0 as amount_orders'),
                        DB::raw('COUNT(payments.id) as count_payments'),
                        DB::raw('SUM(payment_wallet.sum_price) as amount_payments'),
                        DB::raw('DATE(payments.created_at) as action_date'),
                    )
                    ->where('payments.paymentable_type', 'App\Models\Customer')
                    ->where('payments.created_at', '!=', null)
                    ->where('payments.status_id', $statusPaymentCustomer->id)
                    ->groupBy('action_date')
            )
            ->orderBy('action_date')
            ->get();

        $totals = DB::table('orders')
            ->join('completed_orders', 'orders.id', '=', 'completed_orders.order_id')
            ->join('payments', 'orders.customer_id', '=', 'payments.paymentable_id')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->where('payments.paymentable_type', 'App\Models\Customer')
            ->select(
                DB::raw('COUNT(completed_orders.id) as total_count_orders'),
                DB::raw('SUM(completed_orders.total_sale_price) as total_amount_orders'),
                DB::raw('COUNT(payments.id) as total_count_payments'),
                DB::raw('SUM(payment_wallet.sum_price) as total_amount_payments'),
            )
            ->first();

        // Convert Type
        foreach ($data as $index => $entry) {
            $entry->id = $index + 1;
            $entry->count_orders = (int) $entry->count_orders;
            $entry->amount_orders = (float) $entry->amount_orders;
            $entry->count_payments = (int) $entry->count_payments;
            $entry->amount_payments = (float) $entry->amount_payments;
        }

        $response = [
            'data' => $data,
            "total_list" => [
                "total_count_orders" => (int) $totals->total_count_orders,
                "total_amount_orders" => (float) $totals->total_amount_orders,
                "total_count_payments" => (int) $totals->total_count_payments,
                "total_amount_payments" => (float) $totals->total_amount_payments,
                "total_remaining_debt" => (float) $totals->total_amount_orders - (float) $totals->total_amount_payments
            ],
        ];

        return $response;
    }
}
