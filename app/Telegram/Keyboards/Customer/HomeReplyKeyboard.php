<?php

namespace App\Telegram\Keyboards\Customer;

use App\Telegram\Enums\CustomerCommandEnum;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;

class HomeReplyKeyboard
{
  public static function make(): ReplyKeyboard
  {
    return ReplyKeyboard::make()->buttons([
      ReplyButton::make(CustomerCommandEnum::ORDERS->value),
      ReplyButton::make(CustomerCommandEnum::BALANCE->value),
      ReplyButton::make(CustomerCommandEnum::DOCS->value),
      ReplyButton::make(CustomerCommandEnum::NEW_ORDERS->value),
      ])
      ->oneTime()
      ->chunk(2)
      ->resize(true);
  }
}
