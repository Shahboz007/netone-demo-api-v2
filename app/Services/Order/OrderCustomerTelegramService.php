<?php

namespace App\Services\Order;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Telegram\TelegramChat;
use Illuminate\Support\Facades\Log;

class OrderCustomerTelegramService
{
  protected ?Order $order = null;
  protected ?Customer $customer = null;

  public function __construct(protected TelegramChat $telegramChat) {}

  public function setOrderAndCustomer(Order $order, Customer $customer): self
  {
    $this->order = $order;
    $this->customer = $customer;

    return $this;
  }

  // New Order Msg
  public function sendNewOrderMsg(): void
  {
    $this->chat()->message("salom")->send();
  }

  // Process Order Msg
  public function processOrderMsg() {}
  // Cancel Order Msg
  public function cancelOrderMsg() {}
  // Completed Order Msg
  public function CompletedOrderMsg() {}
  // Submitted Order Msg
  public function SubmittedOrderMsg() {}

  private function chat()
  {
    try {
      return TelegramChat::where('chat_id', $this->customer->telegram)
        ->firstOrFail();
    } catch (\Exception $e) {
      Log::error('Telegram chat not found.', [
        'error' => $e->getMessage(),
        'customer_id' => $this->customer->id,
        'time' => now(),
      ]);
    }
  }
}
