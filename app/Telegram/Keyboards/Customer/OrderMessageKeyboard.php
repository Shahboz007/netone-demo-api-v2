<?php

namespace App\Telegram\Keyboards\Customer;

use App\Contracts\TelegramKeyboardInterface;
use App\Telegram\Enums\CustomerCommandEnum;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;

class OrderMessageKeyboard implements TelegramKeyboardInterface
{
  public static function make(): Keyboard
  {
    return Keyboard::make()->buttons([
      Button::make("«")->action('handleOrderPagination')->param('type', "prev"),
      Button::make("»")->action('handleOrderPagination')->param("type", "next"),
    ])->chunk(2);
  }
}
