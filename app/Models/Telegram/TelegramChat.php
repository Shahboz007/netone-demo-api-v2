<?php

namespace App\Models\Telegram;

use DefStudio\Telegraph\Models\TelegraphChat as BaseModel;

class TelegramChat extends BaseModel
{
  protected $table = "telegraph_chats";

  protected $fillable = [
    'chat_id',
    'name',
    'last_message_id',
    'last_command'
  ];
}
