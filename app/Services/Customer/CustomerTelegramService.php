<?php


namespace App\Services\Customer;

use App\Models\Customer;
use App\Telegram\Keyboards\Customer\HomeReplyKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;

class CustomerTelegramService
{
  public function sendMessage(Customer $customer, $message)
  {
    $chat = TelegraphChat::where('chat_id', $customer->telegram)->first();

    if (!$chat) return;

    $chat->markdown($message)->send();
  }

  public function welcome(Customer $customer)
  {
    $chat = TelegraphChat::where('chat_id', $customer->telegram)->first();
    if (!$chat) return;

    $chat->html("<b>👋 Salom, $customer->first_name!</b>\n\nBizning Telegram botimizga qo'shilganingiz uchun tashakkur! Endi xizmatlarimizdan to'liq foydalanishingiz mumkin.")
      ->replyKeyboard(HomeReplyKeyboard::make())
      ->send();
  }

  public function end() {}
}
