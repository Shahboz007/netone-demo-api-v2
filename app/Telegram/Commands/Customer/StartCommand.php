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
    $body = "<b>Assalomu alaykum</b>\n\nNetOnega xush kelibsiz! Siz telegram botimiz yordamida berilgan buyurtmalar, qarzdorlik, to'lovlar va aktsverka hisobotlarini ko'rib borishingiz mumkin\n\n✅ <code>$chatId</code> - <i>bu id raqam yordamida bizning xodimlarimiz sizni tizimga kiritishadi va siz telegram botimizni ishlatishingiz mumkin</i>\n\nSizning holatingiz:\n";

    if ($customer) {
      $chat->html($body."🟢 faol")->replyKeyboard(HomeReplyKeyboard::make())->send();
    } else {
      $chat->html($body."🔴 faolsiz\n<i>Bot imkoniyatidan to'liq foydalana olmaysiz!</i>")->removeReplyKeyboard()->send();
    }
  }
}
