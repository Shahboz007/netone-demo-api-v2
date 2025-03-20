<?php

namespace App\Providers;

use App\Events\Customer\CustomerCreatedEvent;
use App\Events\Customer\CustomerDeletedEvent;
use App\Events\Customer\CustomerTelegramAddedEvent;
use App\Events\Customer\CustomerTelegramRemoveEvent;
use App\Listeners\CustomerDeletedListener;
use App\Listeners\CustomerCreatedListener;
use App\Listeners\CustomerTelegramAddedListener;
use App\Listeners\CustomerTelegramRemoveListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Customer - create
        Event::listen(
            CustomerCreatedEvent::class,
            CustomerCreatedListener::class
        );

        // Customer - delete
        Event::listen(
            CustomerDeletedEvent::class,
            CustomerDeletedListener::class,
        );

        // Customer Telegram - add
        Event::listen(
            CustomerTelegramAddedEvent::class,
            CustomerTelegramAddedListener::class,
        );

        // Customer Telegram - remove
        Event::listen(
            CustomerTelegramRemoveEvent::class,
            CustomerTelegramRemoveListener::class,
        );
    }
}
