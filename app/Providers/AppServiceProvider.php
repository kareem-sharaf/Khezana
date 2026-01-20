<?php

// Khezana Project - Application Service Provider
namespace App\Providers;

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
        // Set default locale to Arabic
        app()->setLocale(config('app.locale', 'ar'));

        // Register event listeners
        Event::listen(
            \App\Events\UserCreated::class,
            [\App\Listeners\LogAdminAction::class, 'handleUserCreated']
        );

        Event::listen(
            \App\Events\UserUpdated::class,
            [\App\Listeners\LogAdminAction::class, 'handleUserUpdated']
        );

        Event::listen(
            \App\Events\UserDeleted::class,
            [\App\Listeners\LogAdminAction::class, 'handleUserDeleted']
        );
    }
}
