<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Models\Customer;
use App\Telegram\Keyboards\Customer\HomeReplyKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;

class StartCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    $chatId = $chat->chat_id;

    // Customer
    $customer = Customer::where('telegram', $chatId)->first();

    // Body
    $message = "<b>Assalomu alaykum</b>\n\n";
    $message .= "NetOnega xush kelibsiz! Siz telegram botimiz yordamida berilgan buyurtmalar, qarzdorlik, to'lovlar va aktsverka hisobotlarini ko'rib borishingiz mumkin\n\n";
    $message .= "✅ <code>$chatId</code> - <i>bu id raqam yordamida bizning xodimlarimiz sizni tizimga kiritishadi va siz telegram botimizni ishlatishingiz mumkin</i>\n\n";
    $message .= "Sizning holatingiz:\n";

    if ($customer) {
      $chat->html($message . "🟢 faol")
        ->replyKeyboard(HomeReplyKeyboard::make())
        ->send();
    } else {
      $chat->html($message . "🔴 faolsiz\n<i>Bot imkoniyatidan to'liq foydalana olmaysiz!</i>")
        ->removeReplyKeyboard()
        ->send();
    }
  }
}
