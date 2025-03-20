<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Services\Order\OrderTelegramService;
use App\Telegram\Actions\Customer\OrderPaginationAction;
use App\Telegram\Enums\CustomerCommandEnum;
use App\Telegram\Keyboards\Customer\OrderPaginationKeyboard;
use App\Telegram\MessageBody\Customer\OrderMessageBody;
use DefStudio\Telegraph\Models\TelegraphChat;

class OrdersCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
   $action = new OrderPaginationAction($chat);

   $action->showPage();
  }
}
