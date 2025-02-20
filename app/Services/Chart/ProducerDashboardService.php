<?php

namespace App\Services\Chart;

use App\Models\Status;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProducerDashboardService
{
    public function get()
    {
        $status = Status::where('code', 'orderSubmitted');

        $todaySales = $this->todaySalesWithDiff();

        return [
            "sales" => $todaySales,
        ];
    }

    private function todaySalesWithDiff()
    {
        $todayAmount = (float) DB::table("completed_orders")
            ->selectRaw('SUM(total_sale_price) as total_sale_price')
            ->whereDate('updated_at', today())
            ->value('total_sale_price');

        $yesterdayAmount = (float) DB::table("completed_orders")
            ->selectRaw('SUM(total_sale_price) as total_sale_price')
            ->whereDate('updated_at', Carbon::yesterday())
            ->value('total_sale_price');

        $diffAmount = $todayAmount - $yesterdayAmount;

        $diffPercent = 100;
        if ($yesterdayAmount != 0) {
            $diffPercent = ($todayAmount ?? 1) * 100 / $yesterdayAmount - 100;
        }


        return [
            "amount" => $todayAmount,
            "diff_amount" => $diffAmount,
            "diff_percent" => $diffPercent,
        ];
    }
}
