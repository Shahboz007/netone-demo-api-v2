<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Telegram\Keyboards\Customer\HomeReplyKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;

class StartCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    $chatId = $chat->chat_id;

    $chat->html("<b>Assalomu alaykum</b>\n\nNetOnega xush kelibsiz! Siz telegram botimiz yordamida berilgan buyurtmalar, qarzdorlik, to'lovlar va aktsverka hisobotlarini ko'rib borishingiz mumkin\n\n✅ <code>$chatId</code> - <i>bu id raqam yordamida bizning xodimlarimiz sizni tizimga kiritishadi va siz telegram botimizni ishlatishingiz mumkin</i>")
      ->replyKeyboard(HomeReplyKeyboard::make())
      ->send();
  }
}
