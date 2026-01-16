<x-auth-layout 
    :title="__('Create Account')" 
    :subtitle="__('Join us today! Create your account in just a few steps.')"
>
    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <!-- Full Name -->
        <x-auth.input
            name="name"
            type="text"
            :label="__('Full Name')"
            required
            autofocus
            autocomplete="name"
            placeholder="{{ __('Enter your full name') }}"
            :hint="__('This will be displayed on your profile')"
        />

        <!-- Phone Number -->
        <x-auth.input
            name="phone"
            type="tel"
            :label="__('Phone Number')"
            required
            autocomplete="tel"
            placeholder="+963 9XX XXX XXX"
            :hint="__('Required for account verification and password recovery. We will never share your number.')"
        />

        <!-- Password -->
        <x-auth.input
            name="password"
            type="password"
            :label="__('Password')"
            required
            autocomplete="new-password"
            placeholder="••••••••"
            :hint="__('At least 8 characters')"
        />

        <!-- Confirm Password -->
        <x-auth.input
            name="password_confirmation"
            type="password"
            :label="__('Confirm Password')"
            required
            autocomplete="new-password"
            placeholder="••••••••"
        />

        <!-- Submit Button -->
        <div class="form-actions">
            <x-auth.button type="submit" variant="primary">
                {{ __('Create Account') }}
            </x-auth.button>
        </div>

        <!-- Login Link -->
        <div class="auth-link-container">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" class="auth-link">
                {{ __('Login') }}
            </a>
        </div>
    </form>
</x-auth-layout>
