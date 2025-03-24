<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderProcessedEvent;

class OrderProcessedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderProcessedEvent $event): void
    {
        //
    }
}
