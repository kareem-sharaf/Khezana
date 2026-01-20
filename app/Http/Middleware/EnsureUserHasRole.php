<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip role check for login page - Filament handles authentication
        if ($request->routeIs('filament.admin.auth.login')) {
            return $next($request);
        }

        // Filament's Authenticate middleware already handles authentication
        // We only need to check the role here
        $user = Auth::user();

        if (!$user || !$user->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized access to admin panel.');
        }

        return $next($request);
    }
}
