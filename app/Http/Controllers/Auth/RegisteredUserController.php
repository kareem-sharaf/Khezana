<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\UserDTO;
use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $redirectUrl = $request->input('redirect');

        return view('auth.register', [
            'redirect' => $redirectUrl,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, UserService $userService): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create user using UserService
        $userDTO = UserDTO::fromArray([
            'name' => $request->name,
            'email' => $request->phone . '@khezana.local', // Temporary email for compatibility
            'phone' => $request->phone,
            'password' => $request->password,
            'status' => 'active',
        ]);

        $user = $userService->create($userDTO);

        // Fire Laravel's Registered event for email verification
        event(new Registered($user));

        Auth::login($user);

        $redirectUrl = $request->input('redirect');

        if ($redirectUrl && filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
            return redirect($redirectUrl)
                ->with('success', 'مرحباً بك! تم إنشاء حسابك بنجاح. يمكنك الآن إتمام العملية.');
        }

        return redirect(route('home', absolute: false))
            ->with('success', 'مرحباً بك! تم إنشاء حسابك بنجاح.');
    }
}
