<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Services\Order\OrderTelegramService;
use App\Telegram\Enums\CustomerCommandEnum;
use App\Telegram\Keyboards\Customer\OrderPaginationKeyboard;
use App\Telegram\MessageBody\Customer\OrderMessageBody;
use DefStudio\Telegraph\Models\TelegraphChat;

class OrdersCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    // Command Name
    $cmdValue = CustomerCommandEnum::ORDERS->value;

    // Service
    $service = new OrderTelegramService();
    $orders = $service->paginate(1);

    $message = "";
    // Order Details List
    foreach ($orders as $order) {
      $message .= OrderMessageBody::makeMessage($order);
    }

    // Pagination
    if ($orders->total() > 1) {
      $message .= OrderPaginationKeyboard::getMsg($orders->currentPage(), $orders->total());
      $chat->html($message)
        ->keyboard(OrderPaginationKeyboard::make($orders->currentPage(), $orders->total()))
        ->send();
    } else {
      $chat->html($message)
        ->send();
    }
  }
}
