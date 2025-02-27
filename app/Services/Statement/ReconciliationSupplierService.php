<?php

namespace App\Services\Statement;

use App\Services\Utils\DateFormater;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReconciliationSupplierService
{
    private string $receiveMsg = "Qabul qilindi";
    private string $returnReceiveMsg = "Qaytarildi";
    private string $paymentMsg = 'O\`tkazma';
    private Carbon|null $startDate = null;
    private Carbon|null $endDate = null;

    public function __construct()
    {
        $this->startDate = Carbon::yesterday();
        $this->endDate = Carbon::today();
    }

    public function setDateInterVal($start, $end): void
    {
        $this->startDate = DateFormater::format($start);
        $this->endDate = DateFormater::format($end);
    }

    public function getBySupplier(string $supplierId)
    {
        $supplierId = 1;

        // Receive
        $receiveProducts = $this->receiveProductsQuery($supplierId);
        // Return Receive
        $returnReceive = $this->returnReceiveProductsQuery($supplierId);
        // Payment Supplier
        $payment = $this->paymentsQuery($supplierId);
        $unionQuery = $receiveProducts
            ->unionAll($returnReceive)
            ->unionAll($payment);

        $data = DB::query()
            ->fromSub($unionQuery, 'sub')
            ->select([
                'action_date',
                // Receive
                DB::raw('SUM(count_received) as count_received'),
                DB::raw('SUM(amount_received) as amount_received'),
                DB::raw('MAX(status_received) as status_received'),
                // Return Receive
                DB::raw('SUM(count_return_received) as count_return_received'),
                DB::raw('SUM(amount_return_received) as amount_return_received'),
                DB::raw('MAX(status_return_received) as status_return_received'),
                // Payment
                DB::raw('SUM(count_payment) as count_payment'),
                DB::raw('SUM(amount_payment) as amount_payment'),
                DB::raw('MAX(status_payment) as status_payment'),
                // Diff
                DB::raw('SUM(amount_diff) as amount_diff'),
            ])
            ->groupBy('action_date')
            ->orderBy('action_date')
            ->get();

        foreach ($data as $item) {
            $item->count_received = (int)$item->count_received;
            $item->amount_received = (float)$item->amount_received;

            $item->count_return_received = (int)$item->count_return_received;
            $item->amount_return_received = (float)$item->amount_return_received;

            $item->count_payment = (int)$item->count_payment;
            $item->amount_payment = (float)$item->amount_payment;

            $item->amount_diff = (float)$item->amount_diff;
        }

        return $data;
    }

    // Receive Products
    private function receiveProductsQuery(int $supplierId): Builder
    {
        return DB::table('receive_products')
            ->select([
                DB::raw('DATE(created_at) as action_date'),
                // Receive
                DB::raw('COUNT(id) as count_received'),
                DB::raw('SUM(total_price) as amount_received'),
                DB::raw("'$this->receiveMsg' as  status_received"),
                // Return Receive
                DB::raw('0 as count_return_received'),
                DB::raw('0 as amount_return_received'),
                DB::raw("NULL as status_return_received"),
                // Payment supplier
                DB::raw('0 as count_payment'),
                DB::raw('0 as amount_payment'),
                DB::raw('NULL as status_payment'),
                // Diff
                DB::raw('SUM(total_price) as amount_diff'),
            ])
            ->where('supplier_id', $supplierId)
            ->groupBy(DB::raw('DATE(created_at)'));
    }

    // Return Receive Products
    private function returnReceiveProductsQuery(int $supplierId): Builder
    {
        return DB::table('return_receives')
            ->select([
                DB::raw('DATE(created_at) as action_date'),
                // Receive
                DB::raw('0 as count_received'),
                DB::raw('0 as amount_received'),
                DB::raw("NULL as  status_received"),
                // Return Receive
                DB::raw('COUNT(id) as count_return_received'),
                DB::raw('SUM(total_sale_price) as amount_return_received'),
                DB::raw("'$this->returnReceiveMsg' as status_return_received"),
                // Payment supplier
                DB::raw('0 as count_payment'),
                DB::raw('0 as amount_payment'),
                DB::raw('NULL as status_payment'),
                // Diff
                DB::raw('SUM(total_sale_price)*-1 as amount_diff'),
            ])
            ->whereNotNull('created_at')
            ->where('supplier_id', $supplierId)
            ->groupBy(DB::raw('DATE(created_at)'));
    }

    // Payment Supplier
    private function paymentsQuery(int $supplierId): Builder
    {
        return DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select([
                DB::raw('DATE(payments.created_at) as action_date'),
                // Receive
                DB::raw('0 as count_received'),
                DB::raw('0 as amount_received'),
                DB::raw("NULL as status_received"),
                // Return Receive
                DB::raw('0 as count_return_received'),
                DB::raw('0 as amount_return_received'),
                DB::raw("NULL as status_return_received"),
                // Payment supplier
                DB::raw('COUNT(payments.id) as count_payment'),
                DB::raw('SUM(payment_wallet.sum_price) as amount_payment'),
                DB::raw("'$this->paymentMsg' as status_payment"),
                // Diff
                DB::raw('SUM(payment_wallet.sum_price)*-1 as amount_diff'),
            ])
            ->whereNotNull('payments.created_at')
            ->where('payments.paymentable_type', 'App\Models\Supplier')
            ->where('payments.paymentable_id', $supplierId)
            ->groupBy(DB::raw('DATE(payments.created_at)'));

    }
}
