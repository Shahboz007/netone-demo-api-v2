<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Telegram\Enums\CustomerCommandEnum;
use DefStudio\Telegraph\Models\TelegraphChat;

class DocsCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    $cmdValue = CustomerCommandEnum::DOCS->value;
    
    $chat->html("<b>$cmdValue</b>\n\n")->send();
  }
}