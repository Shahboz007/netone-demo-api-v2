<?php

namespace App\Providers;

use App\Events\CustomerCreatedEvent;
use App\Events\CustomerDeletedEvent;
use App\Events\CustomerTelegramAddedEvent;
use App\Events\CustomerTelegramRemoveEvent;
use App\Listeners\CustomerDeletedListener;
use App\Listeners\CustomerCreatedListener;
use App\Listeners\CustomerTelegramAddedListener;
use App\Listeners\CustomerTelegramRemoveListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
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
