<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Models\Customer;
use App\Services\Utils\PhoneNumber;
use DefStudio\Telegraph\Models\TelegraphChat;

class BalanceCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    
    // Customer
    $customer = Customer::where('telegram', $chat->chat_id)->firstOrFail();
    
    $message = "<b>Sizning balansingiz!</b>\n\n";

    // Customer
    $message .= "👤Ism:   {$customer->last_name[0]}.$customer->first_name\n";
    $val = PhoneNumber::format($customer->phone);
    $message .= "📞Telefon:   {$val}\n\n";

    
    // Balance
    $val = number_format(max($customer->balance, 0));
    $message .= "💰Balans:   <code>$val uzs</code>\n";

    // Debt
    $val = number_format(min($customer->balance,0));
    $message .= "❌Qarzdorlik:    <code>$val uzs</code>";
    
    $chat->html($message)->send();
  }
}