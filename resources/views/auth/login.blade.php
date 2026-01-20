<x-auth-layout :title="__('Login')" :subtitle="__('Welcome back! Please login to your account.')">
    <!-- Session Status -->
    @if(session('status'))
    <x-auth.alert type="success" :message="session('status')" />
    @endif

    @if(session('message'))
    <x-auth.alert type="info" :message="session('message')" />
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf
        
        @if(request()->has('redirect'))
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif
        
        @if(request()->has('action'))
            <input type="hidden" name="action" value="{{ request('action') }}">
        @endif

        <!-- Phone Number -->
        <x-auth.input
            name="phone"
            type="tel"
            :label="__('Phone Number')"
            required
            autofocus
            placeholder="+963 9XX XXX XXX"
            :hint="__('Enter your registered phone number')"
        />

        <!-- Password -->
        <x-auth.input
            name="password"
            type="password"
            :label="__('Password')"
            required
            autocomplete="current-password"
            placeholder="••••••••"
        />

        <!-- Remember Me & Forgot Password -->
        <div class="form-actions-row">
            <label class="form-checkbox">
                <input type="checkbox" name="remember" id="remember_me">
                <label for="remember_me">{{ __('Remember me') }}</label>
            </label>

            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="auth-link">
                {{ __('Forgot password?') }}
            </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="form-actions">
            <x-auth.button type="submit" variant="primary">
                {{ __('Login') }}
            </x-auth.button>
        </div>

        <!-- Register Link -->
        <div class="auth-link-container">
            <span>{{ __("Don't have an account?") }}</span>
            <a href="{{ route('register') }}" class="auth-link">
                {{ __('Create Account') }}
            </a>
        </div>
    </form>
</x-auth-layout>
