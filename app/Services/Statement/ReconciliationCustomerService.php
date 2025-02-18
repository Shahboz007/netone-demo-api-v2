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

        $completedOrders = $this->getCompletedOrdersQuery($statusSubmittedOrder, $statusPaymentCustomer, $customerId);
        $payments = $this->getPaymentsQuery($statusPaymentCustomer, $customerId);
        $returnOrders  = $this->getOrderReturnsQuery($customerId);

        $unionQuery = $completedOrders
            ->unionAll($payments)
            ->unionAll($returnOrders);



        $data = DB::query()
            ->fromSub($unionQuery, 'sub')
            ->select([
                'action_date',
                DB::raw('SUM(count_orders) as count_orders'),
                DB::raw('SUM(amount_orders) as amount_orders'),
                DB::raw('MAX(order_status) as order_status'),
                DB::raw('SUM(count_payments) as count_payments'),
                DB::raw('SUM(amount_payments) as amount_payments'),
                DB::raw('MAX(payment_status) as payment_status'),
            ])
            ->groupBy('action_date')
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
            ->select([
                DB::raw('DATE(completed_orders.updated_at) as action_date'),
                DB::raw('COUNT(completed_orders.id) as count_orders'),
                DB::raw('SUM(completed_orders.total_sale_price) as amount_orders'),
                DB::raw("'Mijozga berilgan yuklar' as order_status"),
                DB::raw('0 as count_payments'),
                DB::raw('0 as amount_payments'),
                DB::raw('NULL as payment_status'),
            ])
            ->whereNotNull('completed_orders.updated_at')
            ->where('completed_orders.status_id', $statusSubmittedOrder->id)
            ->where('orders.customer_id', $customerId)
            ->groupBy(DB::raw('DATE(completed_orders.updated_at)'));
    }

    private function getPaymentsQuery($statusPaymentCustomer, $customerId)
    {
        return DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select([
                DB::raw('DATE(payments.created_at) as action_date'),
                DB::raw('0 as count_orders'),
                DB::raw('0 as amount_orders'),
                DB::raw('NULL as order_status'),
                DB::raw('COUNT(payments.id) as count_payments'),
                DB::raw('SUM(payment_wallet.sum_price) as amount_payments'),
                DB::raw("'Mijozdan olingan pullar' as payment_status"),
            ])
            ->whereNotNull('payments.created_at')
            ->where('payments.paymentable_type', 'App\Models\Customer')
            ->where('payments.status_id', $statusPaymentCustomer->id)
            ->where('payments.paymentable_id', $customerId)
            ->groupBy(DB::raw('DATE(payments.created_at)'));
    }

    private function getOrderReturnsQuery(int $customerId)
    {
        return DB::table('order_returns')
            ->select([
                DB::raw('DATE(created_at) as action_date'),
                DB::raw('0 as count_orders'),
                // Qaytarilgan summa negativ qiymatda olinadi
                DB::raw('SUM(total_sale_price) * -1 as amount_orders'),
                DB::raw("'Qaytarilgan yuk' as order_status"),
                DB::raw('0 as count_payments'),
                DB::raw('0 as amount_payments'),
                DB::raw('NULL as payment_status'),
            ])
            ->where('customer_id', $customerId)
            ->groupBy(DB::raw('DATE(created_at)'));
    }
}
