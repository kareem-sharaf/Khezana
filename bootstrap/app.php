<?php

// Khezana Project Bootstrap
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'cache.headers' => \App\Http\Middleware\AddCacheHeaders::class,
            'auth.redirect' => \App\Http\Middleware\EnsureAuthenticatedWithRedirect::class,
            'performance.monitor' => \App\Http\Middleware\PerformanceMonitoringMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);

        if (env('APP_DEBUG', false)) {
            $middleware->web(append: [
                \App\Http\Middleware\PerformanceMonitoringMiddleware::class,
            ]);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Phase 5.2: Structured logging for reported exceptions (critical_json)
        $exceptions->reportable(function (\Throwable $e) {
            if (!config('app.debug') || (bool) env('LOG_CRITICAL_JSON', false)) {
                $context = [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
                if (app()->bound('request') && $r = request()) {
                    $context['url'] = $r->fullUrl();
                    $context['method'] = $r->method();
                    if ($r->user()) {
                        $context['user_id'] = $r->user()->getKey();
                    }
                }
                \Illuminate\Support\Facades\Log::channel('critical_json')->error($e->getMessage(), $context);
            }
        });
    })->create();
