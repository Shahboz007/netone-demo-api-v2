<?php

namespace App\Telegram\Handlers;

use App\Telegram\Keyboards\Customer\HomeReplyKeyboard;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use Illuminate\Support\Facades\Log;

class MainWebhookHandler extends WebhookHandler
{
  public function processAction()
  {
    $action = $this->data->get('action');
    $id = $this->data->get('id');
    Log::info('action-test', [$action, $id]);
  }

  public function start()
  {
    $chatId = $this->chat->chat_id;

    $this->chat
      ->html("<b>Assalomu alaykum</b>\n\nNetOnega xush kelibsiz! Siz telegram botimiz yordamida berilgan buyurtmalar, qarzdorlik, to'lovlar va aktsverka hisobotlarini ko'rib borishingiz mumkin\n\n✅ <code>$chatId</code> - <i>bu id raqam yordamida bizning xodimlarimiz sizni tizimga kiritishadi va siz telegram botimizni ishlatishingiz mumkin</i>")
      ->replyKeyboard(HomeReplyKeyboard::make())
      ->send();
  }

  public function delete(string $id): void
  {
    $this->chat->message($id)->send();
  }
}
