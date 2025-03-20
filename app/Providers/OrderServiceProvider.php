<?php

namespace App\Providers;

use App\Events\Order\OrderCreatedEvent;
use App\Listeners\Order\OrderCreatedListener;
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
        // Create
        Event::listen(
            OrderCreatedEvent::class,
            OrderCreatedListener::class
        );

        // Process
        // Cancel
        // Completed
    }
}
