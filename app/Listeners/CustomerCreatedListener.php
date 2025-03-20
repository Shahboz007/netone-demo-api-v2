<?php

namespace App\Listeners;

use App\Services\Customer\CustomerTelegramService;

class CustomerCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected CustomerTelegramService $customerTelegramService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        
        $customer = $event->customer;
        $this->customerTelegramService->welcome($customer);
    }
}
