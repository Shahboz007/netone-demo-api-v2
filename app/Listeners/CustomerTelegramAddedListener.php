<?php

namespace App\Listeners;

use App\Services\Customer\CustomerTelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CustomerTelegramAddedListener
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
        $this->customerTelegramService->addTelegramToCustomer($customer);
    }
}
