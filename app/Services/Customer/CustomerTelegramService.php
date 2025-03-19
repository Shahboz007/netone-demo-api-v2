<?php


namespace App\Services\Customer;

use App\Models\Customer;
use App\Telegram\Keyboards\Customer\HomeReplyKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;

class CustomerTelegramService
{
  // Send Message
  public function sendMessage(Customer $customer, $message)
  {
    $chat = $this->getChat($customer->telegram);

    if (!$chat) return;

    $chat->markdown($message)->send();
  }

  /* Welcome */
  public function welcome(Customer $customer)
  {
    $chat = $this->getChat($customer->telegram);
    if (!$chat) return;

    $chat->html("<b>👋 Salom, $customer->first_name!</b>\n\nBizning Telegram botimizga qo'shilganingiz uchun tashakkur! Endi xizmatlarimizdan to'liq foydalanishingiz mumkin.")
      ->replyKeyboard(HomeReplyKeyboard::make())
      ->send();
  }

  /* Delete Customer Message */
  public function deleteCustomerMessage($customer)
  {
    $chat = $this->getChat($customer->telegram);

    if (!$chat) return;

    $chat->html("<b>❌ Xayr, $customer->first_name!</b>\n\nBiz sizni xizmatlarimizdan o'chirdik. Agar fikringizni o'zgartirsangiz, bizga qaytib qo'shilishingiz mumkin.")
      ->removeReplyKeyboard()
      ->send();
  }

  /* Add Telegram */
  public function addTelegramToCustomer($customer)
  {
    $chat = $this->getChat($customer->telegram);
    if (!$chat) return;

    $chat->html("<b>✅ Tabriklaymiz!</b>\n\nSizning hisobingiz muvaffaqiyatli ravishda Telegram bilan bog'landi! Endi xizmatlarimizdan to'liq foydalanishingiz mumkin.")
      ->replyKeyboard(HomeReplyKeyboard::make())
      ->send();
  }

  /* Remove Telegram */
  public function removeTelegramFromCustomer($customer)
  {
    $chat = $this->getChat($customer->telegram);
    if (!$chat) return;

    $chat->html("<b>❌ Diqqat!</b>\n\nSizning hisobingizdan Telegram chat bog'lanishi o'chirildi.")
      ->removeReplyKeyboard()
      ->send();
  }

  private function getChat(string|null $chat_id)
  {
    return TelegraphChat::where('chat_id', $chat_id)->first();
  }
}
