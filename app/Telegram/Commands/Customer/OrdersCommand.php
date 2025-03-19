<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Telegram\Enums\CustomerCommandEnum;
use App\Telegram\Keyboards\Customer\OrderMessageKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;

class OrdersCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    $cmdValue = CustomerCommandEnum::ORDERS->value;

    $chat->html("<b>$cmdValue</b>\n\n")
      ->keyboard(OrderMessageKeyboard::make())
      ->send();
  }
}
