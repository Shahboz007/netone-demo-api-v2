<?php

namespace App\Services\Statement;

use App\Models\Status;
use Illuminate\Support\Facades\DB;

class ReconciliationCustomerService
{
    public function getByCustomer(string $customerId)
    {
        // Status
        $statusSubmittedOrder = Status::where('code', "orderSubmitted")->firstOrFail();
        $statusPaymentCustomer = Status::where('code', 'paymentCustomer')->firstOrFail();

        $completedOrdersQuery = $this->getCompletedOrdersQuery($statusSubmittedOrder, $statusPaymentCustomer, $customerId);
        $paymentsQuery = $this->getPaymentsQuery($statusPaymentCustomer, $customerId);

        $data = $completedOrdersQuery
            ->union($paymentsQuery)
            ->orderBy('action_date')
            ->get();

        $totals = DB::table('customers')
            ->where('customers.id', $customerId)
            // Mijozning buyurtmalari va ularning yakunlangan ma’lumotlari:
            ->leftJoin('orders', 'customers.id', '=', 'orders.customer_id')
            ->leftJoin('completed_orders', 'orders.id', '=', 'completed_orders.order_id')
            // Mijozning to‘lovlari:
            ->leftJoin('payments', function ($join) use ($statusPaymentCustomer) {
                $join->on('customers.id', '=', 'payments.paymentable_id')
                    ->where('payments.paymentable_type', 'App\\Models\\Customer')
                    ->where('payments.status_id', $statusPaymentCustomer->id);
            })
            ->leftJoin('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select(
                DB::raw('COUNT(DISTINCT completed_orders.id) as total_count_orders'),
                DB::raw('COALESCE(SUM(completed_orders.total_sale_price), 0) as total_amount_orders'),
                DB::raw('COUNT(DISTINCT payments.id) as total_count_payments'),
                DB::raw('COALESCE(SUM(payment_wallet.sum_price), 0) as total_amount_payments')
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

    private function getCompletedOrdersQuery($statusSubmittedOrder, $statusPaymentCustomer, $customerId)
    {
        return DB::table('orders')
            ->join('completed_orders', 'orders.id', '=', 'completed_orders.order_id')
            ->join('statuses', 'orders.status_id', '=', 'statuses.id')
            ->leftJoin('payments', 'orders.customer_id', '=', 'payments.paymentable_id')
            ->leftJoin('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select([
                DB::raw('COUNT(completed_orders.id) as count_orders'),
                DB::raw('SUM(completed_orders.total_sale_price) as amount_orders'),
                DB::raw("'Mijozga berilgan yuklar' as order_status"),
                DB::raw('0 as count_payments'),
                DB::raw('0 as amount_payments'),
                DB::raw('completed_orders.updated_at as action_date'),
                DB::raw('NULL as payment_status'),
            ])
            ->whereNotNull('completed_orders.updated_at')
            ->whereNotNull('payments.created_at')
            ->where('completed_orders.status_id', $statusSubmittedOrder->id)
            ->where('payments.paymentable_type', 'App\Models\Customer')
            ->where('payments.status_id', $statusPaymentCustomer->id)
            ->where('orders.customer_id', $customerId)
            ->groupBy('action_date');
    }

    private function getPaymentsQuery($statusPaymentCustomer, $customerId)
    {
        return DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select([
                DB::raw('0 as count_orders'),
                DB::raw('0 as amount_orders'),
                DB::raw('NULL as order_status'),
                DB::raw('COUNT(payments.id) as count_payments'),
                DB::raw('SUM(payment_wallet.sum_price) as amount_payments'),
                DB::raw('DATE(payments.created_at) as action_date'),
                DB::raw("'Mijozdan olingan pullar' as payment_status"),
            ])
            ->where('payments.paymentable_type', 'App\Models\Customer')
            ->whereNotNull('payments.created_at')
            ->where('payments.status_id', $statusPaymentCustomer->id)
            ->where('payments.paymentable_id', $customerId)
            ->groupBy('action_date');
    }
}
