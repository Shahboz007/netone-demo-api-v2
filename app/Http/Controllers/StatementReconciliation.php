<?php

namespace App\Http\Controllers;

use App\Models\CompletedOrder;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatementReconciliation extends Controller
{
    public function index()
    {
        // Status
        $statusSubmittedOrder = Status::where('code', 'orderSubmitted')->firstOrFail();
        $statusPaymentCustomer = Status::where('code', 'paymentCustomer')->firstOrFail();

        $data = DB::table('orders')
            ->join('completed_orders', 'orders.id', '=', 'completed_orders.order_id')
            ->join('statuses', 'orders.status_id', '=', 'statuses.id')
            ->select(
                DB::raw('COUNT(completed_orders.id) as count_orders'),
                DB::raw('SUM(completed_orders.total_sale_price) as amount_orders'),
                DB::raw('0 as count_payments'),
                DB::raw('0 as amount_payments'),
                DB::raw('orders.created_at as action_date'),
            )
            ->where('completed_orders.created_at', '!=', null)
            ->where('completed_orders.status_id', $statusSubmittedOrder->id)
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
                    ->where('payments.created_at', '!=', null)
                    ->where('payments.status_id', $statusPaymentCustomer->id)
                    ->groupBy('action_date')
            )
            ->orderBy('action_date')
            ->get();

        return ($data);
    }
}
