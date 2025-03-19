<?php

namespace App\Telegram\MessageBody\Customer;

use App\Models\Order;

class OrderMessageBody
{
  public function __construct(protected Order $order) {}

  public static function makeMessage(Order $order): string
  {
    $orderMessageBody = new OrderMessageBody($order);

    if ($order->completedOrder) {
      return $orderMessageBody->completedOrderMsg();
    } else {
      return $orderMessageBody->orderMsg();
    }
  }

  private function orderMsg(): string
  {
    // Order ID
    $message = "<b>" . $this->order->ord_code . "</b>\n\n";

    // Total Price
    $val = number_format($this->order->total_sale_price);
    $message .= "Jami summa:    <b>$val uzs</b>\n";
    // Old Debt
    $message .= "Oldingi qarz:    ...\n";
    // Items Count
    $val = number_format($this->order->orderDetails->count());
    $message .= "Mahsulotlar soni:    <b>$val</b>\n";
    // Status
    $message .= $this->orderStatusMsg();

    // Items
    $message .= "items>>>";

    return $message;
  }

  private function completedOrderMsg(): string
  {
    // Order ID
    $message = "<b>" . $this->order->ord_code . "</b>\n\n";

    // Total Price
    $val = number_format($this->order->completedOrder->total_sale_price);
    $message .= "Jami summa:    <b>$val uzs</b>\n";
    // Old Debt
    $val = number_format($this->order->completedOrder->customer_old_balance);
    $message .= "Oldingi qarz:    <b>$val</b>\n";
    // Items Count
    $val = number_format($this->order->orderDetails->count());
    $message .= "Mahsulotlar soni:    <b>$val</b>\n";
    // Status
    $message .= $this->orderStatusMsg();

    // Items
    $message .= "items>>>";

    return $message;
  }

  private function orderStatusMsg(): string
  {
    $emojis = [
      "orderNew" => "🆕",
      "orderInProgress" => "⏳",
      "orderCancel" => "❌",
      "orderCompleted" => "✅",
      "orderSubmitted" => "📦",
      "orderReturned" => "↩️",
    ];

    $code = $this->order->status->code;
    $name = $this->order->status->name;

    $emoji = $emojis[$code] ?? "";

    return "Holati: $emoji $name\n\n";
  }
}
