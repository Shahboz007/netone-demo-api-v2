<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Telegram\Enums\CustomerCommandEnum;
use DefStudio\Telegraph\Models\TelegraphChat;

class NewOrderCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    $cmdValue = CustomerCommandEnum::NEW_ORDERS->value;

    $chat->html("<b>$cmdValue</b>\n\n Hozircha buyurtma berib bo'lmaydi")->send();
  }
}
