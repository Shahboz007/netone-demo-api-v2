<?php

namespace App\Telegram\Keyboards\Customer;

use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;

class HomeReplyKeyboard
{
  public static function make(): ReplyKeyboard
  {
    return ReplyKeyboard::make()->buttons([
      ReplyButton::make('📦 Buyurtmalarim'),
      // ReplyButton::make('Balans')
    ])
      ->resize(true);
  }
}
