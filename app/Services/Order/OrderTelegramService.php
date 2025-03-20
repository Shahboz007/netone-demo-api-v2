<?php

namespace App\Services\Order;

use App\Models\Order;

class OrderTelegramService
{
  // Get Order Date By Paginate
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

  // New Order Msg
  public function newOrderMsg() {}
  // Process Order Msg
  public function processOrderMsg() {}
  // Cancel Order Msg
  public function cancelOrderMsg() {}
  // Completed Order Msg
  public function CompletedOrderMsg() {}
  // Submitted Order Msg
  public function SubmittedOrderMsg() {}
}
