<?php

namespace App\Contracts;

use DefStudio\Telegraph\Models\TelegraphChat;

interface TelegramCommandInterface
{
  public static function handle(TelegraphChat $chat): void;
}
