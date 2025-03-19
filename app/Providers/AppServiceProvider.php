<?php

namespace App\Providers;

use App\Events\CustomerCreated;
use App\Listeners\CustomerSendWelcomeMessage;
use App\Listeners\CustomerSendWelcomeNotification;
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
            CustomerCreated::class,
            CustomerSendWelcomeNotification::class
        );
    }
}
