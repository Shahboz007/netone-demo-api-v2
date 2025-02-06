<?php

namespace App\Services\Statement;

use App\Models\Status;
use Illuminate\Support\Facades\DB;

class StatementMonthlySales
{
    static public function getMonthlySales(int $year, string $title): array
    {
        $salesStatus = Status::where('code', 'orderSubmitted')->firstOrFail();

        /*------------------------------
            [
                "title": "text",
                "total_sale_price": 0,
                "total_cost_price": 0,
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
        $monthlySubmittedOrders = DB::table('completed_orders')
            ->selectRaw('MIN(id) as id, MONTH(created_at) as month_number, MONTHNAME(created_at) as month_name, SUM(total_sale_price) as sale_price,SUM(total_cost_price) as cost_price')
            ->whereYear('created_at', $year)
            ->where('status_id', $salesStatus->id)
            ->groupByRaw('MONTH(created_at), MONTH(created_at), MONTHNAME(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $totalSalePrice = $monthlySubmittedOrders->sum('sale_price');
        $totalCostPrice = $monthlySubmittedOrders->sum('cost_price');

        $list["title"] = $title;
        $list["total_sale_price"] = $totalSalePrice;
        $list["total_cost_price"] = $totalCostPrice;

        foreach ($monthlySubmittedOrders as $item) {
            $list[$item->month_number]["id"] = $item->id;
            $list[$item->month_number]["month_number"] = $item->month_number;
            $list[$item->month_number]["month_name"] = $item->month_name;
            $list[$item->month_number]["sale_price"] = (float)$item->sale_price;
            $list[$item->month_number]["cost_price"] = (float)$item->cost_price;
        }

        return $list;
    }
}
