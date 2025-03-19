<?php

namespace App\Telegram\Actions\Customer;

use App\Models\Order;
use App\Telegram\Keyboards\Customer\OrderPaginationKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;

class OrderPaginationAction
{


  public function __construct(protected TelegraphChat $chat) {}

  // public function 

  public function prev()
  {
    Log::info("prev_info", ["render"]);
  }
  public function next()
  {
    Log::info("next_info", ["render"]);
  }
}
