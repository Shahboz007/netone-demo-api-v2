<?php

namespace App\Services\Order;

use App\Models\Order;

class OrderTelegramService
{
  public function paginate($customerId, int $perPage = 5, ?int $page = null)
  {
    return Order::with([
      'status',
      'completedOrder',
      'orderDetails.product',
    ])
      ->where('customer_id', $customerId)
      ->paginate($perPage, ['*'], 'page', $page);
  }
}
