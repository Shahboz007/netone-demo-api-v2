<?php

namespace App\Listeners\Order;

use App\Services\Order\OrderCustomerTelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderAddedNewProductListener
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
        $product = $event->product;
        $addData = $event->addData;

        $this->orderCustomerTelegramService->setOrderAndCustomer($order, $customer)
            ->sendAddedNewProductMsg($product, $addData);
    }
}
