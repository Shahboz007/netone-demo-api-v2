<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatementProfitAndLostController extends Controller
{
    public function index()
    {

        $salesStatus = Status::where('code', 'orderSubmitted')->firstOrFail();

        $year = 2024;

        // Order Submitted Status
        $monthlySubmittedOrders = DB::table('completed_orders')
            ->selectRaw('MIN(id) as id, MONTHNAME(created_at) as month_name, SUM(total_sale_price) as total_sales')
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at), MONTHNAME(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        return $monthlySubmittedOrders;
    }
}
