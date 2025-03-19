<?php

namespace App\Services\Order;

use App\Models\Order;

class OrderTelegramService
{
  public function paginate(int $perPage = 5, ?int $page = null)
  {
    return Order::with([
      'status',
      'completedOrder',
      'orderDetails.product',
    ])
      ->paginate($perPage, ['*'], 'page', $page);
  }
}
