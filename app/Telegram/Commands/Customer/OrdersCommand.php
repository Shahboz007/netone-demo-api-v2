<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Services\Order\OrderTelegramService;
use App\Telegram\Enums\CustomerCommandEnum;
use App\Telegram\Keyboards\Customer\OrderMessageKeyboard;
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

    $chat->html($message)
      ->keyboard(OrderMessageKeyboard::make($orders->items()))
      ->send();
  }
}
