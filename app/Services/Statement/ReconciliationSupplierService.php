<?php

namespace App\Services\Statement;

use App\Services\Utils\DateFormatter;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReconciliationSupplierService
{
    private string $receiveMsg = "Qabul qilindi";
    private string $returnReceiveMsg = "Qaytarildi";
    private string $paymentMsg = 'O\`tkazma';
    private string|null $startDate = null;
    private string|null $endDate = null;

    public function __construct()
    {
        $this->startDate = DateFormatter::today('start');
        $this->endDate = DateFormatter::today('end');
    }

    public function setDateInterVal($start, $end): void
    {
        $this->startDate = DateFormatter::format($start, 'start');
        $this->endDate = DateFormatter::format($end, 'end');
    }

    public function getBySupplier(string $supplierId): array
    {

        // All Data Items
        $data = $this->allData($supplierId);

        // Totals
        $totalsReceives = $this->totalReceiveProducts($supplierId);
        $totalsReturns = $this->totalReturnReceiveProducts($supplierId);
        $totalsPayments = $this->totalPayments($supplierId);


        // Receives
        $totalList['total_count_receives'] = (int) $totalsReceives->total_count_receives;
        $totalList['total_amount_receives'] = (float) $totalsReceives->total_amount_receives;
        // Returns
        $totalList['total_count_returns'] = (int) $totalsReturns->total_count_returns;
        $totalList['total_amount_returns'] = (float) $totalsReturns->total_amount_returns;
        // Payment
        $totalList['total_count_payments'] = (int) $totalsPayments->total_count_payments;
        $totalList['total_amount_payments'] = (float) $totalsPayments->total_amount_payments;
        // Diff
        $totalList['total_amount_diff'] = $totalList['total_amount_receives'] - $totalList['total_amount_returns'] - $totalList['total_amount_payments'];

        return [
            'data' => $data,
            "total_list" => $totalList
        ];
    }


    private function allData(int $supplierId): Collection
    {
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
            ->whereBetween('action_date', [$this->startDate, $this->endDate])
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

    /* -------------------------------
    /       TOTALS
    /---------- */
    private function totalReceiveProducts(int $supplierId)
    {
        return DB::table('receive_products')
            ->select([
                DB::raw('SUM(total_price) as total_amount_receives'),
                DB::raw('COUNT(id) as total_count_receives'),
            ])
            ->where('supplier_id', $supplierId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->first();
    }

    private function totalReturnReceiveProducts(int $supplierId)
    {
        return DB::table('return_receives')
            ->select([
                DB::raw('SUM(total_sale_price) as total_amount_returns'),
                DB::raw('COUNT(id) as total_count_returns'),
            ])
            ->where('supplier_id', $supplierId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->first();
    }

    private function totalPayments(int $supplierId)
    {
        return DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select([
                DB::raw('SUM(payment_wallet.sum_price) as total_amount_payments'),
                DB::raw('COUNT(payments.id) as total_count_payments'),
            ])
            ->where('payments.paymentable_id', $supplierId)
            ->where('payments.paymentable_type', 'App\Models\Supplier')
            ->whereBetween('payments.created_at', [$this->startDate, $this->endDate])
            ->first();
    }
}
