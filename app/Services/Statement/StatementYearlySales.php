<?php

namespace App\Services\Statement;

use App\Models\Status;
use Illuminate\Support\Facades\DB;

class StatementYearlySales
{
    private $data = [];
    private float $totalSalePrice = 0;
    private float $totalCostPrice = 0;

    public function __construct($year)
    {
        $salesStatus = Status::where('code', 'orderSubmitted')->firstOrFail();

        /*------------------------------
            [
                "title": "text",
                "total_amount": 0,
                "1": [
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
        $list = [];


        // Completed Orders
        $this->data = DB::table('completed_orders')
            ->selectRaw('MIN(id) as id, MONTH(created_at) as month_number, MONTHNAME(created_at) as month_name, SUM(total_sale_price) as sale_price,SUM(total_cost_price) as cost_price')
            ->whereYear('created_at', $year)
            ->where('status_id', $salesStatus->id)
            ->groupByRaw('MONTH(created_at), MONTH(created_at), MONTHNAME(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $this->totalCostPrice = $this->data->sum('sale_price');
        $this->totalCostPrice = $this->data->sum('cost_price');
    }

    public function getYearlySalePrice(string $title): array
    {
        $list["title"] = $title;


        foreach ($this->data as $item) {
            $list[$item->month_number] = (float)$item->sale_price;;
        }

        $list["total_amount"] = $this->totalSalePrice;

        return $list;
    }

    function getYearlyCostPrice(string $title): array
    {

        $list["title"] = $title;

        foreach ($this->data as $item) {
            $list[$item->month_number] = (float)$item->cost_price;
        }

        $list["total_amount"] = $this->totalCostPrice;

        return $list;
    }

    public function getYearlyMarja(string $title): array
    {
        $list["title"] = $title;

        $totalMarjaAmount = 0;

        foreach ($this->data as $item) {
            $marja = $this->calcMarjaAmount($item->sale_price, $item->cost_price);
            $list[$item->month_number] = $marja;
            $totalMarjaAmount += $marja;
        }

        $list["total_amount"] = $totalMarjaAmount;
        return $list;
    }

    public function getYearlyMarjaByPercent(string $title): array
    {
        $list["title"] = $title;

        $totalMarjaPercent = 0;

        foreach ($this->data as $item) {
            $marja = $this->calcMarjaAmount($item->sale_price, $item->cost_price);
            $percent = round($item->sale_price / $marja * 100, 2);

            $list[$item->month_number] = $percent;
            $totalMarjaPercent += $percent;
        }

        $list["total_amount"] = round($totalMarjaPercent, 2);
        return $list;
    }


    private function calcMarjaAmount(float $salePrice, float $costPrice): float
    {
        return $salePrice - $costPrice;
    }
}
