<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use DefStudio\Telegraph\Models\TelegraphChat;

class SettingsCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    $chat->markdownV2("**Sozlamalar**")
      ->send();
  }
}
