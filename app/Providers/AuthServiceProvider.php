<?php

namespace App\Providers;

use App\Policies\UserWalletPolicy;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
        $this->registerPolicies();

        Gate::define('createUserWallet', [UserWalletPolicy::class, 'create']);
    }
}
