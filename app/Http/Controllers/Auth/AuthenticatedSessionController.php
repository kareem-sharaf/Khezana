<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $redirectUrl = $request->input('redirect');
        $action = $request->input('action');
        $message = session('message') ?? ($redirectUrl ? 'يجب تسجيل الدخول لإتمام هذه العملية' : null);

        return view('auth.login', [
            'redirect' => $redirectUrl,
            'action' => $action,
            'message' => $message,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $redirectUrl = $request->input('redirect');

        if ($redirectUrl && filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
            return redirect($redirectUrl)
                ->with('success', 'مرحباً بك! يمكنك الآن إتمام العملية.');
        }

        return redirect()->intended(route('home', absolute: false))
            ->with('success', 'مرحباً بك!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
