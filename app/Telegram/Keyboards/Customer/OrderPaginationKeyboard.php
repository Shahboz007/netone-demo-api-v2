<?php

namespace App\Telegram\Keyboards\Customer;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class OrderPaginationKeyboard
{
  public static function getMsg(int $current, int $total)
  {
    return "Sahifalar   <b>$current/$total</b>";
  }
  
  /**
   * Generate a keyboard for the given orders.
   *
   * @param \App\Models\Order[] $orders Array of Order objects.
   * @return Keyboard
   */
  public static function make(int $current=1, $total=0): Keyboard
  {
    $keyboard = Keyboard::make();

    
    $keyboard->row([
      Button::make("1")->action('handleOrderPagination')->param("name", "jump")->param("value", 1),
      Button::make("$total")->action('handleOrderPagination')->param("name", "jump")->param('value', $total),
    ]);
    $keyboard->row([
      Button::make("⬅️")->action('handleOrderPagination')->param('name', "navigate")->param("value", "prev"),
      Button::make("$current")->action('handleOrderPagination')->param('name', "disabled")->param("value", $current),
      Button::make("➡️")->action('handleOrderPagination')->param("name", "next")->param("value", "next"),
    ]);
 
    return $keyboard;
  }
}
