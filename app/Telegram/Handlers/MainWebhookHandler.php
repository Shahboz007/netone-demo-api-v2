<?php

namespace App\Telegram\Handlers;

use App\Contracts\TelegramCommandInterface;
use App\Telegram\Commands\Customer\StartCommand;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;

class MainWebhookHandler extends WebhookHandler
{
  protected array $commands = [
    '/start' => StartCommand::class,
  ];

  public function handleCommand(Stringable $text): void
  {
    $command = $text->toString();

    if (isset($this->commands[$command])) {
      $handler = $this->commands[$command];

      if (is_subclass_of($handler, TelegramCommandInterface::class)) {
        $handler::handle($this->chat);
      } else {
        $this->chat->html("❌ Command handler for <code>{$command}</code> is invalid.")->send();
      }
    }
  }
}
