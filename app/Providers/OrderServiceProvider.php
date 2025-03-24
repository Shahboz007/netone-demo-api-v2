<?php

namespace App\Providers;

use App\Events\Order\OrderAddedNewProductEvent;
use App\Events\Order\OrderCompletedEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderProcessedEvent;
use App\Events\Order\OrderSubmittedEvent;
use App\Listeners\Order\OrderAddedNewProductListener;
use App\Listeners\Order\OrderCompletedListener;
use App\Listeners\Order\OrderCreatedListener;
use App\Listeners\Order\OrderProcessedListener;
use App\Listeners\Order\OrderSubmittedListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
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
        // Created
        Event::listen(
            OrderCreatedEvent::class,
            OrderCreatedListener::class
        );

        // Processed
        Event::listen(
            OrderProcessedEvent::class,
            OrderProcessedListener::class,
        );

        // Added New Product
        Event::listen(
            OrderAddedNewProductEvent::class,
            OrderAddedNewProductListener::class
        );

        // Cancel

        // Completed
        Event::listen(
            OrderCompletedEvent::class,
            OrderCompletedListener::class
        );

        // Submitted
        Event::listen(
            OrderSubmittedEvent::class,
            OrderSubmittedListener::class
        );
    }
}
