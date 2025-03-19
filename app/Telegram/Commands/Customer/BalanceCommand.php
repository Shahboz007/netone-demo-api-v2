<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Telegram\Enums\CustomerCommandEnum;
use DefStudio\Telegraph\Models\TelegraphChat;

class BalanceCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    $cmdValue = CustomerCommandEnum::BALANCE->value;
    
    $chat->html("<b>$cmdValue</b>\n\n UZS: 0")->send();
  }
}