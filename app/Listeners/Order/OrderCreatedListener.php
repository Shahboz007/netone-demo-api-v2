<?php

namespace App\Listeners\Order;

use App\Services\Order\OrderCustomerTelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class OrderCreatedListener
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
        $this->orderCustomerTelegramService
            ->setOrderAndCustomer($order, $customer)
            ->sendNewOrderMsg();
    }
}
