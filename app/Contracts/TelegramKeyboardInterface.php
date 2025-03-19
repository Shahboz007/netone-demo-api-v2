<?php

namespace App\Contracts;

use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;

interface TelegramKeyboardInterface
{
  public static function make(): ReplyKeyboard|Keyboard;
}
