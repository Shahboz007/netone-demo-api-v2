<?php

namespace App\Telegram\Handlers;

use App\Contracts\TelegramCommandInterface;
use App\Telegram\Commands\Customer\BalanceCommand;
use App\Telegram\Commands\Customer\DocsCommand;
use App\Telegram\Commands\Customer\NewOrderCommand;
use App\Telegram\Commands\Customer\OrdersCommand;
use App\Telegram\Commands\Customer\StartCommand;
use App\Telegram\Enums\CustomerCommandEnum;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;

class MainWebhookHandler extends WebhookHandler
{
  protected array $commands = [
    CustomerCommandEnum::START->value => StartCommand::class,
    CustomerCommandEnum::ORDERS->value => OrdersCommand::class,
    CustomerCommandEnum::BALANCE->value => BalanceCommand::class,
    CustomerCommandEnum::DOCS->value => DocsCommand::class,
    CustomerCommandEnum::NEW_ORDERS->value => NewOrderCommand::class,
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

  public function handleChatMessage($text): void
  {
    $command = $text->toString();

    if (isset($this->commands[$command])) {
      $handler = $this->commands[$command];
      if (is_subclass_of($handler, TelegramCommandInterface::class)) {
        Log::info("chatttting 2", [$command]);
        $handler::handle($this->chat);
      } else {
        $this->chat->html("❌ Command handler for <code>{$command}</code> is invalid.")->send();
      }
    }
  }
}
