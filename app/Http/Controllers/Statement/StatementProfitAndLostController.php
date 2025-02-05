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
        $year = 2024;

        $salesStatus = Status::where('code', 'orderSubmitted')->firstOrFail();

        $list = $this->getAllMonth();


        // Completed Orders
        $monthlySubmittedOrders = DB::table('completed_orders')
            ->selectRaw('MIN(id) as id, MONTH(created_at) as month_number, MONTHNAME(created_at) as month_name, SUM(total_sale_price) as sale_price')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', 1)
            ->where('status_id', $salesStatus->id)
            ->groupByRaw('MONTH(created_at), MONTH(created_at), MONTHNAME(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        //

        foreach ($monthlySubmittedOrders as $item) {
            $index = array_search($item->month_number, array_column($list, 'month_number'));

            if ($index !== false) {
                $list[$index]['sale_price'] = $item->sale_price;
            }
        }

        return $list;
    }

    public function getAllMonth(): array
    {
        return [
            $this->generateMonth(1, 'January'),
            $this->generateMonth(2, 'February'),
            $this->generateMonth(3, 'March'),
            $this->generateMonth(4, 'April'),
            $this->generateMonth(5, 'May'),
            $this->generateMonth(6, 'June'),
            $this->generateMonth(7, 'July'),
            $this->generateMonth(8, 'August'),
            $this->generateMonth(9, 'September'),
            $this->generateMonth(10, 'October'),
            $this->generateMonth(11, 'November'),
            $this->generateMonth(12, 'December'),
        ];
    }

    public function generateMonth(int $number, string $month): array
    {
        return [
            'month_number' => $number,
            'month_name' => $month,
            'sale_price' => 0,
        ];
    }
}
