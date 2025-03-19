<?php

namespace App\Telegram\Actions\Customer;

use App\Models\Order;
use App\Telegram\Keyboards\Customer\OrderMessageKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;

class OrderPaginationAction
{


  public function __construct(protected TelegraphChat $chat) {}

  public function prev()
  {
    Log::info("prev_info", ["render"]);
  }
  public function next()
  {
    Log::info("next_info", ["render"]);
  }

  public function howPage(int $page = 1)
  {
    $orders = Order::with('status')
      ->paginate(1, ['*'], 'page', $page);

    $message = "Buyurtmaringiz (sahifa $page):\n\n";
    foreach ($orders as $order) {
      $message .= "🛒 Buyurtma ID: {$order->id}, Holati: {$order->status->name}\n";
    }

    $keyboard = OrderMessageKeyboard::make($orders->items());

    $this->chat->html("Buyurtmalaringiz:")
      ->keyboard($keyboard)
      ->send();
  }
}
