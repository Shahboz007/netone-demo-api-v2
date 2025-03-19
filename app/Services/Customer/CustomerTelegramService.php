<?php


namespace App\Services\Customer;

use App\Models\Customer;
use App\Telegram\Keyboards\Customer\HomeReplyKeyboard;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Models\TelegraphChat;

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

    $chat->html("<b>👋 Salom, {$customer->name}!\n\nBizning Telegram botimizga qo'shilganikngiz uchun tashakkur! Endi xizmatlarimizdan to'liq foydalanishingiz mumkin.")
      ->keyboard(HomeReplyKeyboard::make())
      ->send();
  }

  public function end() {}
}
