<?php

namespace App\Services\Chart;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProducerDashboardService
{
    public function get()
    {
        $todaySales = $this->todaySales();
        $todayExpense = $this->todayExpense();
        $todayIncome = $this->todayIncome($todaySales, $todayExpense);

        return [
            "sales" => $todaySales,
            "expense" => $todayExpense,
            "income" => $todayIncome,
        ];
    }

    private function todaySales()
    {
        $todayAmount = (float) DB::table("completed_orders")
            ->selectRaw('SUM(total_sale_price) as total_sale_price')
            ->whereDate('updated_at', today())
            ->value('total_sale_price');

        $todayCostAmount = (float) DB::table("completed_orders")
            ->selectRaw('SUM(total_sale_price) as total_sale_price')
            ->whereDate('updated_at', today())
            ->value('total_cost_price');

        $yesterdayAmount = (float) DB::table("completed_orders")
            ->selectRaw('SUM(total_sale_price) as total_sale_price')
            ->whereDate('updated_at', Carbon::yesterday())
            ->value('total_sale_price');

        $diffAmount = $todayAmount - $yesterdayAmount;

        $diffPercent = 100;
        if ($yesterdayAmount != 0) {
            $diffPercent = ($todayAmount ?? 1) * 100 / $yesterdayAmount - 100;
        }else if($todayAmount == 0){
            $diffPercent = 0;
        }


        return [
            'yesterday_amount' => $yesterdayAmount,
            "today_amount" => $todayAmount,

            "today_profit" => $todayAmount - $todayCostAmount,
            "today_cost_amount" => $todayCostAmount,
            
            "diff_amount" => $diffAmount,
            "diff_percent" => $diffPercent,
        ];
    }

    private function todayExpense()
    {
        $todayExpense = DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select(
                DB::raw('COUNT(payments.id) as total_count'),
                DB::raw('SUM(payment_wallet.sum_price) as total_amount'),
            )
            ->where('payments.paymentable_type', 'App\Models\Expense')
            ->whereDate('payments.created_at', today())
            ->first();

        $yesterdayExpense = DB::table('payments')
            ->join('payment_wallet', 'payments.id', '=', 'payment_wallet.payment_id')
            ->select(
                DB::raw('COUNT(payments.id) as total_count'),
                DB::raw('SUM(payment_wallet.sum_price) as total_amount'),
            )
            ->where('payments.paymentable_type', 'App\Models\Expense')
            ->whereDate('payments.created_at', Carbon::yesterday())
            ->first();

        // Amounts
        $todayAmount = (float) $todayExpense->total_amount;
        $yesterdayAmount = (float) $yesterdayExpense->total_amount;

        $diffAmount = $todayAmount - $yesterdayAmount;
        $diffPercent = 100;
        if ($yesterdayAmount != 0) {
            $diffPercent = ($todayAmount ?? 1) * 100 / $yesterdayAmount - 100;
        }else if($todayAmount == 0){
            $diffPercent = 0;
        }

        return [
            "yesterday_amount" => $yesterdayAmount,
            "yesterday_count" => $yesterdayExpense->total_count,
            "today_amount" => $todayAmount,
            "today_count" => $todayExpense->total_count,
            "diff_amount" => $diffAmount,
            "diff_percent" => $diffPercent
        ];
    }

    private function todayIncome($todaySales, $todayExpense)
    {
        return [
            "amount" => $todaySales['today_profit'] - $todayExpense['today_amount']
        ];
    }
}
