<?php

namespace App\Telegram\Handlers;

use DefStudio\Telegraph\Handlers\WebhookHandler;

class MainWebhookHandler extends WebhookHandler
{
  public function start()
  {
    $this->chat->markdown("*Hi* happy to be here!")->send();
  }
}
