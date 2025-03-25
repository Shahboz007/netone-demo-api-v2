<?php

namespace App\Services\Statement;

use App\Services\Utils\DateFormatter;
use Illuminate\Support\Facades\DB;

class ProductProfitMarginService
{
  public function findAll(array $params)
  {
    // Params
    $startDate = DateFormatter::format($params['startDate']);
    $endDate = DateFormatter::format($params['endDate'], 'end');


    // Query
    $query = DB::table('products')
      ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
      ->select(
        'products.id as product_id',
        'products.name as product_name',
        DB::raw('COALESCE(SUM(order_details.sum_sale_price - order_details.sum_cost_price), 0) as total_profit')
      )
      ->whereBetween('order_details.created_at', [$startDate, $endDate])
      ->groupBy('products.id', 'products.name')
      ->orderByDesc('total_profit');

    $data = $query->get();


    foreach ($data as $item) {
      $item->total_profit = (float) $item->total_profit;
    }

    return [
      'data' => $data,
      'totals' => [
        'total_count' => $data->count(),
        'total_amount' => $data->sum('total_profit')
      ],
    ];
  }
}
