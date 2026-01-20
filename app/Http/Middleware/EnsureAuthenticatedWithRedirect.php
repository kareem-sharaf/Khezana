<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticatedWithRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guest()) {
            $redirectUrl = $request->fullUrl();
            $action = $request->route()->getName();
            
            $loginUrl = route('login', [
                'redirect' => $redirectUrl,
                'action' => $action,
            ]);
            
            return redirect($loginUrl)
                ->with('message', 'يجب تسجيل الدخول لإتمام هذه العملية');
        }

        return $next($request);
    }
}
