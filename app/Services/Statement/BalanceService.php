<?php

namespace App\Services\Statement;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class BalanceService
{

    // Balance
    public function getBalance()
    {
        $totalSumStockPrice = $this->getAllProductStockPrice();
        $totalSumCustomerDebt = $this->getAllCustomerDebt();
        $totalSumSupplierDebt = $this->getAllSupplierDebt();

        $totalSumDiff = $totalSumStockPrice + $totalSumCustomerDebt - abs($totalSumSupplierDebt);

        return [
            "total_sum_stock" => $totalSumStockPrice,
            "total_sum_customer" => $totalSumCustomerDebt,
            "total_sum_supplier" => $totalSumSupplierDebt,
            "total_sum_diff" => $totalSumDiff,
        ];
    }

    private function getAllProductStockPrice(): float
    {
        $amount = DB::table('products')
            ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
            ->selectRaw('SUM(products.sale_price * product_stocks.amount ) as total_value')
            ->value('total_value');

        return (float) $amount;
    }
    private function getAllCustomerDebt(): float
    {
        $amount = (float) Customer::sum('balance');
        return min($amount, 0);
    }

    private function getAllSupplierDebt(): float
    {
        $amount = (float) Supplier::sum('balance') * -1;
        return min($amount, 0);
    }
}
