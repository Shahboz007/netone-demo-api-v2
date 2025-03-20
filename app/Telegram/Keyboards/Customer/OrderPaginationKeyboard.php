<?php

namespace App\Telegram\Keyboards\Customer;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class OrderPaginationKeyboard
{
  public static function getMsg(int $current, int $total)
  {
    return "Buyurtmalar:   <b>$current/$total</b>";
  }

  /**
   * Generate a keyboard for the given orders.
   *
   * @param \App\Models\Order[] $orders Array of Order objects.
   * @return Keyboard
   */
  public static function make(int $currentPage = 1, $lastPage = 0): Keyboard
  {
    $keyboard = Keyboard::make();

    $prev = $currentPage > 1 ? $currentPage - 1 : 1;
    $next = $currentPage < $lastPage ? $currentPage + 1 : $lastPage;

    $keyboard->row([
      self::btn(1, 1),

      self::btn($lastPage, $currentPage),
    ]);
    $keyboard->row([
      self::btn("⬅️", $prev),
      self::btn($currentPage, $currentPage),
      self::btn("➡️", $next),
    ]);

    return $keyboard;
  }

  private static function btn(string $label, int $val): Button
  {
    return  Button::make($label)
      ->action('handleOrderPagination')
      ->param("value", $val);
  }
}
