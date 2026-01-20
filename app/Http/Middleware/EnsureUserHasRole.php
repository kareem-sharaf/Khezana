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
        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = Auth::user();

        // Check if user has admin or super_admin role
        if (!$user->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized access to admin panel.');
        }

        return $next($request);
    }
}
