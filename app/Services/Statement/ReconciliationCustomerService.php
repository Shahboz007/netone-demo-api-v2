<?php

namespace App\Services\Statement;

use App\Models\Status;
use Illuminate\Support\Facades\DB;

class ReconciliationCustomerService
{
    private $orderMsg = "Berilgan yuk";
    private $returnOrderMsg = "Qaytarilgan yuk";
    private $paymentMsg = "Olingan pul";

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
                // Order
                DB::raw('SUM(count_orders) as count_orders'),
                DB::raw('SUM(amount_orders) as amount_orders'),
                DB::raw('MAX(order_status) as order_status'),
                // Return Order
                DB::raw('SUM(count_order_returns) as count_order_returns'),
                DB::raw('SUM(amount_order_returns) as amount_order_returns'),
                DB::raw('MAX(order_return_status) as order_return_status'),
                // Difference
                DB::raw('SUM(amount_difference) as amount_difference'),
                // Payment
                DB::raw('SUM(count_payments) as count_payments'),
                DB::raw('SUM(amount_payments) as amount_payments'),
                DB::raw('MAX(payment_status) as payment_status'),
            ])
            ->groupBy('action_date')
            ->orderBy('action_date')
            ->get();

        $totals = DB::table('customers')
            ->where('customers.id', $customerId)
            ->leftJoin('orders', 'customers.id', '=', 'orders.customer_id')
            ->leftJoin('completed_orders', 'orders.id', '=', 'completed_orders.order_id')
            ->leftJoin('payments', function ($join) use ($statusPaymentCustomer) {
                $join->on('customers.id', '=', 'payments.paymentable_id')
                    ->where('payments.paymentable_type', 'App\\Models\\Customer')
                    ->where('payments.status_id', $statusPaymentCustomer->id);
            })
            ->leftJoin('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->leftJoin('order_returns', 'customers.id', '=', 'order_returns.customer_id')
            ->select(
                // Order
                DB::raw('COUNT(DISTINCT completed_orders.id) as total_count_orders'),
                DB::raw('COALESCE(SUM(completed_orders.total_sale_price), 0) as total_amount_orders'),
                // Payment
                DB::raw('COUNT(DISTINCT payments.id) as total_count_payments'),
                DB::raw('COALESCE(SUM(payment_wallet.sum_price), 0) as total_amount_payments'),
                // Return Orders
                DB::raw('COUNT(DISTINCT order_returns.id) as total_count_returns'),
                DB::raw('COALESCE(SUM(order_returns.total_sale_price), 0) as total_amount_returns'),
            )
            ->first();

        // Convert Type
        foreach ($data as $index => $entry) {
            $entry->id = $index + 1;
            // Order
            $entry->count_orders = (int) $entry->count_orders;
            $entry->amount_orders = (float) $entry->amount_orders;
            // Return Order
            $entry->count_order_returns = (int) $entry->count_order_returns;
            $entry->amount_order_returns = (float) $entry->amount_order_returns;
            // Difference
            $entry->amount_difference = (float) $entry->amount_difference;
            // Payments
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
                "total_count_order_returns" => (int) $totals->total_count_returns,
                "total_amount_order_returns" => (float) $totals->total_amount_returns,
                "total_amount_difference" => (float) $totals->total_amount_orders - (float) $totals->total_amount_returns - (float) $totals->total_amount_payments
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
                // Order
                DB::raw('COUNT(completed_orders.id) as count_orders'),
                DB::raw('SUM(completed_orders.total_sale_price) as amount_orders'),
                DB::raw("'$this->orderMsg' as order_status"),
                // Order Return
                DB::raw('0 as amount_order_returns'),
                DB::raw('0 as count_order_returns'),
                DB::raw('NULL as order_return_status'),
                // Order Diff
                DB::raw('SUM(completed_orders.total_sale_price) as amount_difference'),
                // Payment
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
                // Order
                DB::raw('0 as count_orders'),
                DB::raw('0 as amount_orders'),
                DB::raw('NULL as order_status'),
                // Return Order
                DB::raw('0 as count_order_returns'),
                DB::raw('0 as amount_order_returns'),
                DB::raw('NULL as order_return_status'),
                // Order Diff
                DB::raw('SUM(payment_wallet.sum_price) * -1 as amount_difference'),
                // Payment
                DB::raw('COUNT(payments.id) as count_payments'),
                DB::raw('SUM(payment_wallet.sum_price) as amount_payments'),
                DB::raw("'$this->paymentMsg' as payment_status"),
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
                // Order
                DB::raw('0 as count_orders'),
                DB::raw('0 as amount_orders'),
                DB::raw("NULL as order_status"),
                // Return Order
                DB::raw('SUM(total_sale_price) as amount_order_returns'),
                DB::raw('111 as count_order_returns'),
                DB::raw("'$this->returnOrderMsg' as order_return_status"),
                // Order Diff
                DB::raw('SUM(total_sale_price) * -1 as amount_difference'),
                // Payment
                DB::raw('0 as count_payments'),
                DB::raw('0 as amount_payments'),
                DB::raw('NULL as payment_status'),
            ])
            ->where('customer_id', $customerId)
            ->groupBy(DB::raw('DATE(created_at)'));
    }
}
