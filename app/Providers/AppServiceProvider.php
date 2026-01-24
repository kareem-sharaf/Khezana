<?php

// Khezana Project - Application Service Provider
namespace App\Providers;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Sentry\Laravel\Integration;

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

        // Cache invalidation listeners
        Event::listen(
            \App\Events\Approval\ContentApproved::class,
            \App\Listeners\InvalidateItemCache::class
        );
        Event::listen(
            \App\Events\Approval\ContentApproved::class,
            \App\Listeners\InvalidateRequestCache::class
        );
        Event::listen(
            \App\Events\Approval\ContentRejected::class,
            \App\Listeners\InvalidateItemCache::class
        );
        Event::listen(
            \App\Events\Approval\ContentRejected::class,
            \App\Listeners\InvalidateRequestCache::class
        );
        Event::listen(
            \App\Events\Approval\ContentArchived::class,
            \App\Listeners\InvalidateItemCache::class
        );
        Event::listen(
            \App\Events\Approval\ContentArchived::class,
            \App\Listeners\InvalidateRequestCache::class
        );

        // Phase 5.2: Sentry user context on login
        Event::listen(Authenticated::class, function (Authenticated $event): void {
            $user = $event->user;
            Integration::configureScope(static function (\Sentry\State\Scope $scope) use ($user): void {
                $scope->setUser([
                    'id' => (string) $user->getKey(),
                    'email' => $user->email ?? null,
                ]);
            });
        });

        // Phase 0.1: Slow Query Logging for Performance Monitoring
        if (config('app.debug')) {
            DB::listen(function ($query) {
                // Log queries that take more than 100ms
                if ($query->time > 100) {
                    Log::warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time_ms' => $query->time,
                        'connection' => $query->connectionName,
                    ]);
                }
            });
        }
    }
}
