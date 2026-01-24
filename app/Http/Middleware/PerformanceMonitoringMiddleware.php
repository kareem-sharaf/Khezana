<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Performance\PerformanceMonitoringService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to monitor request performance
 * 
 * Records performance metrics for each request to track improvements
 */
class PerformanceMonitoringMiddleware
{
    public function __construct(
        private readonly PerformanceMonitoringService $monitoringService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

        // Record metrics for important routes
        if ($this->shouldMonitor($request)) {
            $operation = $this->getOperationName($request);
            $this->monitoringService->recordMetric($operation, $duration, [
                'method' => $request->method(),
                'route' => $request->route()?->getName(),
                'status' => $response->getStatusCode(),
            ]);
        }

        return $response;
    }

    /**
     * Determine if this request should be monitored
     */
    private function shouldMonitor(Request $request): bool
    {
        // Monitor important routes only
        $importantRoutes = [
            'items.create',
            'items.store',
            'items.index',
            'items.show',
            'items.search',
        ];

        $routeName = $request->route()?->getName();
        
        return in_array($routeName, $importantRoutes) || 
               $request->is('items*') ||
               $request->is('/');
    }

    /**
     * Get operation name for metrics
     */
    private function getOperationName(Request $request): string
    {
        $routeName = $request->route()?->getName();
        
        return match ($routeName) {
            'items.store' => 'item_creation',
            'items.index', 'items.search' => 'page_load',
            default => 'request',
        };
    }
}
