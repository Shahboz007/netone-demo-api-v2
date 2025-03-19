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
    $chat = $this->getChat($customer->telegram);

    if (!$chat) return;

    $chat->markdown($message)->send();
  }

  public function welcome(Customer $customer)
  {
    $chat = $this->getChat($customer->telegram);
    if (!$chat) return;

    $chat->html("<b>👋 Salom, $customer->first_name!</b>\n\nBizning Telegram botimizga qo'shilganingiz uchun tashakkur! Endi xizmatlarimizdan to'liq foydalanishingiz mumkin.")
      ->replyKeyboard(HomeReplyKeyboard::make())
      ->send();
  }

  public function deleteMessage($customer)
  {
    $chat = $this->getChat($customer->telegram);

    if (!$chat) return;

    $chat->html("<b>❌ Xayr, $customer->first_name!</b>\n\nBiz sizni xizmatlarimizdan o'chirdik. Agar fikringizni o'zgartirsangiz, bizga qaytib qo'shilishingiz mumkin.")
      ->removeReplyKeyboard()
      ->send();
  }

  private function getChat(string $chat_id)
  {
    return TelegraphChat::where('chat_id', $chat_id)->first();
  }
}
