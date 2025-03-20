<?php

namespace App\Telegram\Commands\Customer;

use App\Contracts\TelegramCommandInterface;
use App\Telegram\Actions\Customer\OrderPaginationAction;
use DefStudio\Telegraph\Models\TelegraphChat;

class OrdersCommand implements TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void
  {
    $action = new OrderPaginationAction($chat);

    $action->showPage();
  }
}
