<?php

namespace App\Providers;

use App\Events\CustomerCreatedEvent;
use App\Listeners\CustomerSendWelcomeMessage;
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
        Event::listen(
            CustomerCreatedEvent::class,
            CustomerCreatedListener::class
        );
    }
}
