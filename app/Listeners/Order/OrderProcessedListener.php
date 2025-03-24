<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderProcessedEvent;
use App\Services\Order\OrderCustomerTelegramService;

class OrderProcessedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected OrderCustomerTelegramService $orderCustomerTelegramService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {

        $order = $event->order;
        $customer = $order->customer;
        $this->orderCustomerTelegramService->setOrderAndCustomer($order, $customer)
            ->sendProcessOrderMsg();
    }
}
