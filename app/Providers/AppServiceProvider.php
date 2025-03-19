<?php

namespace App\Providers;

use App\Events\CustomerCreatedEvent;
use App\Events\CustomerDeletedEvent;
use App\Listeners\CustomerDeletedListener;
use App\Listeners\CustomerCreatedListener;
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

        // customer - delete
        Event::listen(
            CustomerDeletedEvent::class,
            CustomerDeletedListener::class,
        );
    }
}
