<?php

namespace App\Services\Statement;

use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StatementYearlySales
{
    private $completedOrderData = [];
    private $returnedOrderData = [];
    private float $totalSalePrice = 0;
    private float $totalCostPrice = 0;

    public function __construct($year)
    {
        $salesStatus = Status::where('code', 'orderSubmitted')->firstOrFail();

        /*------------------------------
            [
                "id": "123asd",
                "title": "text",
                "total_amount": 0,
                "month_number_1": [
                       "id": 300,
                       "month_number": 1,
                       "month_name": "January",
                       "sale_price": 0,
                       "cost_price": 0
                 ],
                // and other month
                // ....
            ]
        --*/


        // Completed Orders
        $this->completedOrderData = DB::table('completed_orders')
            ->selectRaw('
                MIN(id) as id,
                MONTH(created_at) as month_number,
                MONTHNAME(created_at) as month_name,
                SUM(total_sale_price) as sale_price,
                SUM(total_cost_price) as cost_price
            ')
            ->whereYear('created_at', $year)
            ->where('status_id', $salesStatus->id)
            ->groupByRaw('MONTH(created_at), MONTHNAME(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $this->returnedOrderData = DB::table('order_returns')
            ->selectRaw('
                MIN(id) as id,
                MONTH(created_at) as month_number,
                MONTHNAME(created_at) as month_name,
                SUM(total_sale_price) as sale_price,
                SUM(total_cost_price) as cost_price
            ')
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at), MONTHNAME(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $this->totalSalePrice = $this->completedOrderData->sum('sale_price');
        $this->totalCostPrice = $this->completedOrderData->sum('cost_price');
    }

    public function getYearlyProfit(array $params): array
    {
        // List
        $list = $this->createList($params);

        foreach ($this->completedOrderData as $item) {
            $list["month_number_$item->month_number"] = (float)$item->sale_price;;
        }

        $list["total_amount"] = $this->totalSalePrice;

        return $list;
    }

    public function getYearlyReturnOrder(array $params): array
    {
        // List
        $list = $this->createList($params);

        $sum = 0;
        foreach ($this->returnedOrderData as $item) {
            $amount = (float)$item->sale_price;
            $sum += $amount;
            $list["month_number_$item->month_number"] = $amount;
        }

        $list["total_amount"] = $sum;

        return $list;
    }

    public function getYearlyNetProfit(array $params): array
    {
        $list = $this->createList($params);

        // Total Amount
        $totalAmount = 0;

        $completedOrderList = $this->completedOrderData->toArray();
        $returnedOrderList = $this->returnedOrderData->toArray();

        for ($i = 0; $i < count($completedOrderList); $i++) {

            if (isset($completedOrderList[$i])) {
                $profitItem = $completedOrderList[$i];

                $monthNumber = $profitItem->month_number;
                $profitItemSalePrice = $profitItem->sale_price;

                $amount = $profitItemSalePrice;

                if (isset($returnedOrderList[$i])) {
                    $amount = $profitItemSalePrice - $returnedOrderList[$i]->sale_price;
                }

                $list["month_number_$monthNumber"] = $amount;
                $totalAmount += $amount;
            }
        }

        $list["total_amount"] = $totalAmount;

        return $list;
    }

    public function getYearlyCostPrice(array $params): array
    {
        // List
        $list = $this->createList($params);

        foreach ($this->completedOrderData as $item) {
            $list["month_number_$item->month_number"] = (float)$item->cost_price;
        }

        $list["total_amount"] = $this->totalCostPrice;

        return $list;
    }

    public function getYearlyMarja(array $params): array
    {
        // List
        $list = $this->createList($params);

        $totalMarjaAmount = 0;

        foreach ($this->completedOrderData as $item) {
            $marja = $this->calcMarjaAmount($item->sale_price, $item->cost_price);
            $list["month_number_$item->month_number"] = $marja;
            $totalMarjaAmount += $marja;
        }

        $list["total_amount"] = $totalMarjaAmount;
        return $list;
    }

    public function getYearlyMarjaByPercent(array $params): array
    {
        // List
        $list = $this->createList($params);

        $totalMarjaPercent = 0;

        foreach ($this->completedOrderData as $item) {
            $marja = $this->calcMarjaAmount($item->sale_price, $item->cost_price);
            $percent = round($marja / $item->sale_price * 100, 2);

            $list["month_number_$item->month_number"] = $percent;
            $totalMarjaPercent += $percent;
        }

        $list["total_amount"] = round($totalMarjaPercent, 2);
        return $list;
    }

    public function getYearlyShippingRawMaterial(array $params): array
    {
        // List
        $list = $this->createList($params);

        foreach ($this->completedOrderData as $item) {
            $list["month_number_$item->month_number"] = 0;
        }

        $list["total_amount"] = 0;

        return $list;
    }

    private function getID(): string
    {
        return Str::uuid()->toString();
    }

    private function createList(array $params): array
    {
        $list['id'] = $this->getID();

        if ($params['title']) $list['title'] = $params['title'];
        $list['strong'] = isset($params['strong']);
        $list['is_color'] = isset($params['is_color']);
        $list['is_diff'] = isset($params['is_diff']);
        return $list;
    }

    private function calcMarjaAmount(float $salePrice, float $costPrice): float
    {
        return $salePrice - $costPrice;
    }
}
