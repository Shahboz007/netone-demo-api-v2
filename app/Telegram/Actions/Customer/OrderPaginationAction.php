<?php

namespace App\Telegram\Actions\Customer;

use App\Models\Customer;
use App\Models\Order;
use App\Services\Order\OrderTelegramService;
use App\Telegram\Keyboards\Customer\OrderPaginationKeyboard;
use App\Telegram\MessageBody\Customer\OrderMessageBody;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;

class OrderPaginationAction
{
  public function __construct(protected TelegraphChat $chat) {}

  // public function 

  public function showPage($page = 1)
  {
    Log::info("render0action",[$page]);
    // Customer
    $customer = Customer::where('telegram', $this->chat->chat_id)->first();
    
    // Service
    $service = new OrderTelegramService();
    $orders = $service->paginate($customer->id,1, $page);

    $message = "";
    // Order Details List
    foreach ($orders as $order) {
      $message .= OrderMessageBody::makeMessage($order);
    }

    // Pagination
    if ($orders->total() > 1) {
      $message .= OrderPaginationKeyboard::getMsg($orders->currentPage(), $orders->total());
      $this->chat->html($message)
        ->keyboard(OrderPaginationKeyboard::make($orders->currentPage(), $orders->total()))
        ->send();
    } else {
      $this->chat->html($message)
        ->send();
    }
  }
}
