<?php

namespace App\Telegram\Keyboards\Customer;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class OrderMessageKeyboard
{
  /**
   * Generate a keyboard for the given orders.
   *
   * @param \App\Models\Order[] $orders Array of Order objects.
   * @return Keyboard
   */
  public static function make(array $orders): Keyboard
  {
    $keyboard = Keyboard::make();

    $keyboard->buttons([
      Button::make("«")->action('handleOrderPagination')->param('type', "prev"),
      Button::make("»")->action('handleOrderPagination')->param("type", "next"),
    ])->chunk(2);

    return $keyboard;
  }
}
