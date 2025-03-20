<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Models\Customer;
use App\Services\Utils\PhoneNumber;
use DefStudio\Telegraph\Models\TelegraphChat;

class SettingsCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    $message = "<b>Sozlamalar</>\n\n";

    // Customer
    $customer = Customer::where('telegram', $chat->chat_id)->firstOrFail();

    // Name
    $message .= "👤Ism:   {$customer->last_name[0]}.$customer->first_name\n";
    $val = PhoneNumber::format($customer->phone);

    // Phone
    $message .= "📞Telefon:   {$val}\n";

    // Status
    $message .= "🟢Holati:    Faol\n\n";

    $chat->html($message)
      ->send();
  }
}
