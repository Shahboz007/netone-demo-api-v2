<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Telegram\TelegramChat;
use DefStudio\Telegraph\Models\TelegraphChat;

class OrderTelegramService
{
  public function __construct(
    protected TelegramChat $telegramChat
  ) {}

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
  public function newOrderMsg(Order $order) {}
}
